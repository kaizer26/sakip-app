<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\Indikator;
use Illuminate\Http\Request;

class CapaianKinerjaController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', \App\Models\Setting::get('default_tahun', date('Y')));
        $triwulan = $request->get('triwulan', \App\Models\Setting::get('default_triwulan', min(ceil(date('n') / 3), 4)));

        // Get indicators visible to current user with target and realisasi for current triwulan
        $indikators = Indikator::visibleTo(auth()->user())
            ->with(['target', 'realisasis' => function ($query) use ($triwulan) {
                $query->where('triwulan', $triwulan);
            }, 'analisis' => function ($query) use ($triwulan) {
                $query->where('triwulan', $triwulan);
            }])
            ->orderBy('kode')
            ->get();

        // Get existing capaian data for this period
        $capaians = CapaianKinerja::where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->whereIn('indikator_id', $indikators->pluck('id'))
            ->get()
            ->keyBy('indikator_id');

        return view('capaian_kinerja.index', compact('indikators', 'capaians', 'tahun', 'triwulan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'tahun' => 'required|integer',
            'triwulan' => 'required|integer|between:1,4',
            'link_bukti_kinerja' => 'nullable|string',
            'link_bukti_tindak_lanjut' => 'nullable|string',
            'penjelasan_lainnya' => 'nullable|string',
            'dasar_hitung' => 'nullable|string',
            'argumen_logis' => 'nullable|string',
            'realisasi_kumulatif' => 'required|numeric',
            'realisasi_x' => 'nullable|numeric|min:0',
            'realisasi_y' => 'nullable|numeric|min:0',
        ]);

        // Authorization check
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $indikator = Indikator::find($validated['indikator_id']);
            if (!$indikator || $indikator->pic_id != $user->pegawai_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk indikator ini.'
                ], 403);
            }
        }

        $indikatorId = $validated['indikator_id'];
        $tw = $validated['triwulan'];

        // Validation TW > Previous TW
        $previous = \App\Models\Realisasi::where('indikator_id', $indikatorId)
            ->where('triwulan', '<', $tw)
            ->orderBy('triwulan', 'desc')
            ->first();

        if ($previous && $validated['realisasi_kumulatif'] < $previous->realisasi_kumulatif) {
            return response()->json([
                'status' => 'error',
                'message' => "Nilai kumulatif tidak boleh lebih kecil dari Triwulan sebelumnya ({$previous->realisasi_kumulatif})"
            ], 422);
        }

        // Save numeric realization
        \App\Models\Realisasi::updateOrCreate(
            ['indikator_id' => $indikatorId, 'triwulan' => $tw],
            [
                'realisasi_kumulatif' => $validated['realisasi_kumulatif'],
                'realisasi_x'         => $validated['realisasi_x'] ?? null,
                'realisasi_y'         => $validated['realisasi_y'] ?? null,
            ]
        );

        // Upsert qualitative capaian data
        $capaian = CapaianKinerja::updateOrCreate(
            [
                'indikator_id' => $indikatorId,
                'tahun' => $validated['tahun'],
                'triwulan' => $tw,
            ],
            [
                'link_bukti_kinerja' => $validated['link_bukti_kinerja'] ?: null,
                'link_bukti_tindak_lanjut' => $validated['link_bukti_tindak_lanjut'] ?: null,
                'penjelasan_lainnya' => $validated['penjelasan_lainnya'] ?: null,
                'dasar_hitung' => $validated['dasar_hitung'] ?: null,
                'argumen_logis' => $validated['argumen_logis'] ?: null,
            ]
        );

        // Analisis data is handled via the dashboard reporting (PublicInputController)

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Capaian kinerja berhasil disimpan.',
                'data' => $capaian,
            ]);
        }

        return redirect()->route('capaian-kinerja.index', [
            'tahun' => $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ])->with('success', 'Capaian kinerja berhasil disimpan.');
    }

    public function getDataPrevious(Request $request, $indikatorId)
    {
        $tahun = $request->get('tahun', date('Y'));
        $triwulan = $request->get('triwulan');

        $capaian = CapaianKinerja::where('indikator_id', $indikatorId)
            ->where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->first();

        if ($capaian && ($capaian->dasar_hitung || $capaian->argumen_logis || $capaian->penjelasan_lainnya)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'dasar_hitung' => $capaian->dasar_hitung ?? '',
                    'argumen_logis' => $capaian->argumen_logis ?? '',
                    'penjelasan_lainnya' => $capaian->penjelasan_lainnya ?? ''
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
    public function import(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'triwulan' => 'required|integer|between:1,4',
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengimport capaian.');
        }

        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\CapaianKinerjaImport($request->tahun, $request->triwulan),
                $request->file('file')
            );
            return back()->with('success', 'Data Capaian Kinerja berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function template()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengunduh template.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'Kode Indikator',
            'Realisasi TW',
            'Kendala yg Dihadapi',
            'Solusi yg Telah Dilakukan',
            'Rencana Tindak Lanjut',
            'PIC Tindak Lanjut',
            'Batas Waktu TL',
            'Link Bukti Dukung Kinerja',
            'Link Bukti Dukung Rencana Tindak Lanjut Triwulan Sebelumnya'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $indikators = Indikator::orderBy('kode')->get();
        $row = 2;
        foreach ($indikators as $ind) {
            $sheet->setCellValue('A' . $row, $ind->kode);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Import_Capaian_Kinerja.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }
}
