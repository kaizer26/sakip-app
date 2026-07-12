<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutputMaster;
use App\Models\Indikator;
use App\Imports\OutputMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class OutputMasterController extends Controller
{
    public function index()
    {
        $query = OutputMaster::with('indikator');
        
        // Filter: Admin sees all, PIC Indikator sees outputs under their indicators
        if (!auth()->user()->isAdmin()) {
            $pegawaiId = auth()->user()->pegawai_id;
            if ($pegawaiId) {
                // Show outputs where the indicator: 
                // 1. Has this user as PIC
                // 2. Has an activity (kegiatan) where this user is leader or member
                $query->whereHas('indikator', function($q) use ($pegawaiId) {
                    $q->where('pic_id', $pegawaiId)
                      ->orWhereHas('kegiatanMasters', function($sq) use ($pegawaiId) {
                          $sq->where('ketua_tim_id', $pegawaiId)
                            ->orWhereHas('anggotas', function($ssq) use ($pegawaiId) {
                                $ssq->where('pegawai_id', $pegawaiId);
                            });
                      });
                });
            } else {
                // If user doesn't have a linked pegawai profile, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        $outputs = $query->get();
        $indikators = Indikator::all();

        // Filter: Admin sees all, PIC Indikator sees outputs and indicators they responsible for
        if (!auth()->user()->isAdmin()) {
            $pegawaiId = auth()->user()->pegawai_id;
            if ($pegawaiId) {
                // Dropdown indicators only shows what they are PIC of 
                // or have an activity in
                $indikators = Indikator::where('pic_id', $pegawaiId)
                    ->orWhereHas('kegiatanMasters', function($q) use ($pegawaiId) {
                        $q->where('ketua_tim_id', $pegawaiId)
                          ->orWhereHas('anggotas', function($sq) use ($pegawaiId) {
                              $sq->where('pegawai_id', $pegawaiId);
                          });
                    })->get();
            } else {
                $indikators = collect();
            }
        }
        
        return view('admin.output.index', compact('outputs', 'indikators'));
    }

    public function store(Request $request)
    {
        $pegawaiId = auth()->user()->pegawai_id;
        $isAdmin = auth()->user()->isAdmin();

        $validated = $request->validate([
            'indikator_id' => [
                'required',
                'exists:indikators,id',
                function ($attribute, $value, $fail) use ($isAdmin, $pegawaiId) {
                    if (!$isAdmin) {
                        $indikator = Indikator::find($value);
                        if (!$indikator || $indikator->pic_id != $pegawaiId) {
                            $fail('Anda hanya diperbolehkan menambah output pada indikator di mana Anda adalah PIC.');
                        }
                    }
                },
            ],
            'nama_output'    => 'required|string',
            'penjelasan_ro'  => 'nullable|string',
            'target_volume'  => 'nullable|numeric|min:0',
            'jenis_output'   => 'required|in:Laporan,Publikasi',
            'periode'        => 'required|in:Tahunan,Triwulanan,Bulanan',
            'file'           => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('outputs', 'public');
        }

        $output = OutputMaster::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Output berhasil ditambahkan',
                'data' => $output->load('indikator'),
                'file_url' => $output->file_path ? asset('storage/' . $output->file_path) : null
            ]);
        }

        return redirect()->route('output-master.index')->with('success', 'Master Output berhasil ditambahkan.');
    }

    public function show(OutputMaster $outputMaster)
    {
        return response()->json(array_merge(
            $outputMaster->load('indikator')->toArray(),
            ['file_url' => $outputMaster->file_path ? asset('storage/' . $outputMaster->file_path) : null]
        ));
    }

    public function update(Request $request, OutputMaster $outputMaster)
    {
        $pegawaiId = auth()->user()->pegawai_id;
        $isAdmin = auth()->user()->isAdmin();

        $validated = $request->validate([
            'indikator_id' => [
                'required',
                'exists:indikators,id',
                function ($attribute, $value, $fail) use ($isAdmin, $pegawaiId) {
                    if (!$isAdmin) {
                        $indikator = Indikator::find($value);
                        if (!$indikator || $indikator->pic_id != $pegawaiId) {
                            $fail('Anda hanya diperbolehkan memperbarui output pada indikator di mana Anda adalah PIC.');
                        }
                    }
                },
            ],
            'nama_output'    => 'required|string',
            'penjelasan_ro'  => 'nullable|string',
            'target_volume'  => 'nullable|numeric|min:0',
            'jenis_output'   => 'required|in:Laporan,Publikasi',
            'periode'        => 'required|in:Tahunan,Triwulanan,Bulanan',
            'file'           => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($outputMaster->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($outputMaster->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($outputMaster->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('outputs', 'public');
        }

        $outputMaster->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Output berhasil diperbarui',
                'data' => $outputMaster->load('indikator'),
                'file_url' => $outputMaster->file_path ? asset('storage/' . $outputMaster->file_path) : null
            ]);
        }

        return redirect()->route('output-master.index')->with('success', 'Master Output berhasil diperbarui.');
    }

    public function destroy(OutputMaster $outputMaster)
    {
        $outputMaster->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Master Output berhasil dihapus'
            ]);
        }

        return redirect()->route('output-master.index')->with('success', 'Master Output berhasil dihapus.');
    }

    public function toggleStatus(OutputMaster $outputMaster)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $outputMaster->indikator->pic_id != $user->pegawai_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $outputMaster->update([
            'is_achieved' => !$outputMaster->is_achieved
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status output berhasil diperbarui',
            'is_achieved' => $outputMaster->is_achieved
        ]);
    }

    public function uploadFile(Request $request, OutputMaster $outputMaster)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $outputMaster->indikator->pic_id != $user->pegawai_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:51200',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($outputMaster->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($outputMaster->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($outputMaster->file_path);
            }

            $path = $request->file('file')->store('outputs', 'public');
            
            $outputMaster->update([
                'file_path' => $path
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumen berhasil diunggah',
                'file_path' => $path,
                'file_url' => asset('storage/' . $path),
                'file_name' => basename($path)
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'No file uploaded'], 400);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new OutputMasterImport, $request->file('file'));
        return redirect()->route('output-master.index')->with('success', 'Data Master Output berhasil diimport.');
    }

    public function downloadTemplate()
    {
        $headers = ['kode_indikator', 'nama_output', 'jenis_output', 'periode'];
        
        return Excel::download(new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $headers;
            public function __construct($headers) { 
                $this->headers = $headers;
            }
            public function array(): array { 
                return [$this->headers]; 
            }
        }, 'template_import_output.xlsx');
    }
}
