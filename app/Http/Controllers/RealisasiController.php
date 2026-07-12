<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\OutputRealisasi;
use App\Http\Requests\RealisasiRequest;
use Illuminate\Http\Request;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        $triwulan = $request->get('triwulan', 1);
        $realisasis = Realisasi::with(['indikator.pic'])->where('triwulan', $triwulan)->get();
        return view('realisasi.index', compact('realisasis', 'triwulan'));
    }

    public function entry(Indikator $indikator)
    {
        $user = auth()->user();
        $isPIC = $user->isAdmin() || ($user->pegawai_id && $indikator->pic_id == $user->pegawai_id);
        return view('realisasi.entry', compact('indikator', 'isPIC'));
    }

    public function getContext(Indikator $indikator, $triwulan)
    {
        $targetRecord = \App\Models\Target::where('indikator_id', $indikator->id)->first();
        $targetField = 'target_tw' . $triwulan;
        $targetXField = 'target_x_tw' . $triwulan;
        $targetYField = 'target_y_tw' . $triwulan;

        $targetVal = $targetRecord ? $targetRecord->$targetField : '-';
        $targetXVal = $targetRecord ? $targetRecord->$targetXField : null;
        $targetYVal = $targetRecord ? $targetRecord->$targetYField : null;

        $previousRealisasi = Realisasi::where('indikator_id', $indikator->id)
            ->where('triwulan', '<', $triwulan)
            ->orderBy('triwulan', 'desc')
            ->first();

        $currentRealisasi = Realisasi::where('indikator_id', $indikator->id)
            ->where('triwulan', $triwulan)
            ->first();

        // Fix N+1: eager-load pegawai via relasi (pegawai_nip → nip)
        $aktivitas = $indikator->aktivitas()
            ->where('triwulan', $triwulan)
            ->with('pegawai')
            ->get();

        $aktivitasFormatted = $aktivitas->map(function ($a) {
            return [
                'pegawai'              => $a->pegawai ? $a->pegawai->nama : $a->pegawai_nip,
                'uraian'               => $a->uraian,
                'tahapan'              => $a->tahapan,
                'tanggal'              => $a->tanggal_mulai . ' - ' . $a->tanggal_selesai,
                'lampirans'            => $a->lampiran ?? [],
                'penjelasan_kegiatan'  => $a->penjelasan_kegiatan,
                'realisasi_kegiatan'   => $a->realisasi_kegiatan,
            ];
        });

        // Fix N+1: eager-load pegawai pada analisis
        $analisis = $indikator->analisis()
            ->where('triwulan', $triwulan)
            ->with('pegawai')
            ->get();

        $analisisFormatted = $analisis->map(function ($a) {
            return [
                'pegawai'  => $a->pegawai ? $a->pegawai->nama : $a->pegawai_nip,
                'kendala'  => $a->kendala,
                'solusi'   => $a->solusi,
                'severity' => $a->severity,
                'tanggal'  => $a->created_at->format('Y-m-d'),
            ];
        });

        $outputs = $indikator->outputMasters()->with(['outputRealisasis' => function ($q) use ($triwulan) {
            $q->where('triwulan', $triwulan);
        }])->get();

        return response()->json([
            'target'         => $targetVal,
            'target_x'       => $targetXVal,
            'target_y'       => $targetYVal,
            'previous_value' => $previousRealisasi ? $previousRealisasi->realisasi_kumulatif : 0,
            'current_value'  => $currentRealisasi ? $currentRealisasi->realisasi_kumulatif : null,
            'current_x'      => $currentRealisasi ? $currentRealisasi->realisasi_x : null,
            'current_y'      => $currentRealisasi ? $currentRealisasi->realisasi_y : null,
            'definisi_x'     => $indikator->definisi_x,
            'definisi_y'     => $indikator->definisi_y,
            'aktivitas'      => $aktivitasFormatted,
            'analisis'       => $analisisFormatted,
            'outputs'        => $outputs->map(function ($o) {
                $realisasi = $o->outputRealisasis->first();
                return [
                    'id'             => $o->id,
                    'nama_output'    => $o->nama_output,
                    'jenis_output'   => $o->jenis_output,
                    'penjelasan_ro'  => $o->penjelasan_ro,
                    'target_volume'  => $o->target_volume,
                    'is_achieved'    => $o->is_achieved,
                    'file_path'      => $o->file_path,
                    'volume'         => $realisasi ? $realisasi->volume : null,
                    'progres'        => $realisasi ? $realisasi->progres : null,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $indikator = Indikator::findOrFail($request->indikator_id);

        if (!$user->isAdmin() && $indikator->pic_id != $user->pegawai_id) {
            abort(403, 'Anda bukan PIC untuk indikator ini.');
        }

        $validated = $request->validate([
            'indikator_id'        => 'required|exists:indikators,id',
            'triwulan'            => 'required|integer|between:1,4',
            'realisasi_kumulatif' => 'required|numeric',
            'realisasi_x'         => 'nullable|numeric|min:0',
            'realisasi_y'         => 'nullable|numeric|min:0',
            'output_data'         => 'nullable|array',
            'output_data.*.volume'  => 'nullable|numeric',
            'output_data.*.progres' => 'nullable|numeric|between:0,100',
        ]);

        $indikatorId = $validated['indikator_id'];
        $tw = $validated['triwulan'];

        // Validation TW > Previous TW
        $previous = Realisasi::where('indikator_id', $indikatorId)
            ->where('triwulan', '<', $tw)
            ->orderBy('triwulan', 'desc')
            ->first();

        if ($previous && $validated['realisasi_kumulatif'] < $previous->realisasi_kumulatif) {
            return redirect()->back()->withErrors([
                'realisasi_kumulatif' => "Nilai kumulatif tidak boleh lebih kecil dari Triwulan sebelumnya ({$previous->realisasi_kumulatif})"
            ])->withInput();
        }

        $realisasi = Realisasi::where('indikator_id', $indikatorId)
            ->where('triwulan', $tw)
            ->first();

        $action = $realisasi ? 'updated' : 'created';
        $oldValue = $realisasi ? $realisasi->realisasi_kumulatif : null;

        $realisasi = Realisasi::updateOrCreate(
            ['indikator_id' => $indikatorId, 'triwulan' => $tw],
            [
                'realisasi_kumulatif' => $validated['realisasi_kumulatif'],
                'realisasi_x'         => $validated['realisasi_x'] ?? null,
                'realisasi_y'         => $validated['realisasi_y'] ?? null,
            ]
        );

        // Save Output Realisasis
        if ($request->has('output_data')) {
            foreach ($request->output_data as $outputId => $data) {
                \App\Models\OutputRealisasi::updateOrCreate(
                    ['output_master_id' => $outputId, 'triwulan' => $tw],
                    ['volume' => $data['volume'] ?? 0, 'progres' => $data['progres'] ?? 0]
                );
            }
        }

        // Record Log
        if ($oldValue != $validated['realisasi_kumulatif']) {
            \App\Models\RealisasiLog::create([
                'realisasi_id' => $realisasi->id,
                'user_id' => auth()->id(),
                'old_value' => $oldValue,
                'new_value' => $validated['realisasi_kumulatif'],
                'action' => $action
            ]);
        }

        return redirect()->route('rekap.capaian')->with('success', 'Realisasi berhasil disimpan');
    }

    public function history(Realisasi $realisasi)
    {
        // Only Admin
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = $realisasi->logs()->with('user')->latest()->get();
        return response()->json($logs);
    }
}
