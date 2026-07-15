<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\IndikatorMedia;
use App\Http\Requests\IndikatorRequest;
use App\Imports\IndikatorImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        $query = Indikator::with('pic')
            ->withCount([
                'kegiatanMasters',
                'outputMasters',
                'outputMasters as completed_outputs_count' => function ($q) {
                    $q->where('is_achieved', true);
                }
            ]);
        
        if (!auth()->user()->isAdmin()) {
            $pegawaiId = auth()->user()->pegawai_id;
            if ($pegawaiId) {
                // Show indicators where user is PIC
                $query->where('pic_id', $pegawaiId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $indikators = $query->get();
        $pegawais = \App\Models\Pegawai::orderBy('pangkat_golongan', 'desc')->orderBy('nip', 'asc')->get();
        return view('indikator.index', compact('indikators', 'pegawais'));
    }

    public function store(IndikatorRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menambahkan indikator.');
        }

        $indikator = Indikator::create($request->validated());
        $indikator->target()->create();
        
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Indikator berhasil ditambahkan',
                'data' => $indikator
            ]);
        }
        
        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil ditambahkan');
    }

    public function show(Indikator $indikator)
    {
        return response()->json($indikator);
    }

    public function update(IndikatorRequest $request, Indikator $indikator)
    {
        $data = $request->validated();
        $user = auth()->user();

        if (!$user->isAdmin()) {
            if ($indikator->pic_id != $user->pegawai_id) {
                abort(403, 'Anda bukan PIC untuk indikator ini.');
            }
            // PIC cannot change the PIC field
            unset($data['pic_id']);
        }

        $indikator->update($data);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Indikator berhasil diperbarui',
                'data' => $indikator
            ]);
        }
        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil diperbarui');
    }

    public function updateTautan(Request $request, Indikator $indikator)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $indikator->pic_id != $user->pegawai_id) {
            abort(403);
        }

        $validated = $request->validate([
            'dasar_hitung' => 'nullable|string',
        ]);

        $indikator->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Dasar Hitung & Tautan berhasil diperbarui',
                'data' => $indikator
            ]);
        }

        return redirect()->route('indikator.index')->with('success', 'Dasar Hitung & Tautan berhasil diperbarui');
    }

    public function destroy(Indikator $indikator)
    {
        $indikator->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Indikator berhasil dihapus'
            ]);
        }

        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new IndikatorImport, $request->file('file'));

        return redirect()->route('indikator.index')->with('success', 'Data Indikator berhasil diimport.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'kode', 'kode_tujuan', 'tujuan', 'kode_sasaran', 'sasaran', 
            'kode_indikator_kinerja', 'indikator_kinerja', 
            'jenis_indikator', 'periode', 'tipe', 'satuan', 
            'target_tahunan', 'tahun'
        ];

        return Excel::download(new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $headers;
            public function __construct($headers) { $this->headers = $headers; }
            public function array(): array { return [$this->headers]; }
        }, 'template_import_indikator.xlsx');
    }

    /**
     * Simpan konten rich-text: basis_data, dasar_hitung, definisi_x, definisi_y.
     */
    public function updateRichContent(Request $request, Indikator $indikator)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $indikator->pic_id != $user->pegawai_id) {
            abort(403);
        }

        $validated = $request->validate([
            'basis_data'   => 'nullable|string',
            'dasar_hitung' => 'nullable|string',
            'definisi_x'   => 'nullable|string|max:500',
            'definisi_y'   => 'nullable|string|max:500',
        ]);

        $indikator->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Konten berhasil disimpan',
        ]);
    }

    /**
     * Upload foto/media untuk disematkan ke dalam rich-text editor.
     * Return URL foto yang bisa langsung digunakan oleh Quill.js.
     */
    public function uploadMedia(Request $request, Indikator $indikator)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $indikator->pic_id != $user->pegawai_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file'  => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'field' => 'required|in:basis_data,dasar_hitung',
        ]);

        $file     = $request->file('file');
        $field    = $request->input('field');
        $path     = $file->store("indikator_media/{$indikator->id}", 'public');

        // Simpan metadata media ke tabel indikator_media
        $media = IndikatorMedia::create([
            'indikator_id'  => $indikator->id,
            'field'         => $field,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
        ]);

        return response()->json([
            'status' => 'success',
            'url'    => asset('storage/' . $path),
            'id'     => $media->id,
        ]);
    }
}
