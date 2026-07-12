<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Pegawai;
use App\Models\Aktivitas;
use App\Models\Analisis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicInputController extends Controller
{
    public function storeKendala(Request $request)
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;

        $validated = $request->validate([
            'indikator_id' => 'required|exists:indikators,id',
            'triwulan' => 'required|integer|between:1,4',
            'kendala' => 'required|string',
            'severity' => 'required|in:Low,Medium,High',
            'solusi' => 'nullable|string',
            'rencana_tindak_lanjut' => 'nullable|string',
            'pic_tindak_lanjut' => 'nullable|string',
            'batas_waktu' => 'nullable|date',
            'kegiatan_id' => 'required|exists:kegiatan_masters,id',
            'pegawai_nip' => 'required|string',
        ]);

        $kegiatan = \App\Models\KegiatanMaster::find($validated['kegiatan_id']);
        $isKetuaTim = $kegiatan->ketua_tim_id == $pegawai->id;

        Analisis::create([
            'indikator_id' => $validated['indikator_id'],
            'pegawai_nip' => $validated['pegawai_nip'],
            'triwulan' => $validated['triwulan'],
            'kendala' => $validated['kendala'],
            'severity' => $validated['severity'],
            'solusi' => $validated['solusi'],
            'rencana_tindak_lanjut' => $isKetuaTim ? $validated['rencana_tindak_lanjut'] : null,
            'pic_tindak_lanjut' => $isKetuaTim ? $validated['pic_tindak_lanjut'] : ($pegawai->nama),
            'batas_waktu' => $isKetuaTim ? $validated['batas_waktu'] : null,
            'kegiatan_id' => $validated['kegiatan_id'],
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim.');
    }

    public function getKegiatan($indikator_id)
    {
        $user = auth()->user();
        $pegawai_id = $user->pegawai_id;

        $query = \App\Models\KegiatanMaster::where('indikator_id', $indikator_id);

        if (!$user->isAdmin()) {
            $query->where(function($q) use ($pegawai_id) {
                $q->where('ketua_tim_id', $pegawai_id)
                  ->orWhereHas('anggotas', function($q2) use ($pegawai_id) {
                      $q2->where('pegawai_id', $pegawai_id);
                  });
            });
        }

        $kegiatans = $query->get(['id', 'nama_kegiatan', 'tahapan_json', 'ketua_tim_id']);
        
        // Add anggota list for Ketua Tim selection
        $kegiatans->map(function($k) {
            $k->anggotas_list = $k->anggotas()->get(['nama']);
            return $k;
        });

        return response()->json($kegiatans);
    }

    public function storeAktivitas(Request $request)
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;

        $validated = $request->validate([
            'indikator_id'        => 'required|exists:indikators,id',
            'kegiatan_id'         => 'required|exists:kegiatan_masters,id',
            'triwulan'            => 'required|integer|between:1,4',
            'tahapan'             => 'required|string',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
            'uraian'              => 'required|string',
            'penjelasan_kegiatan' => 'nullable|string',
            'realisasi_kegiatan'  => 'nullable|string',
            'lampiran.*'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,csv|max:10240',
            'pegawai_nip'         => 'required|string',
        ]);

        $paths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $paths[] = $file->store('lampiran', 'public');
            }
        }

        \App\Models\Aktivitas::create([
            'indikator_id'        => $validated['indikator_id'],
            'kegiatan_id'         => $validated['kegiatan_id'],
            'pegawai_nip'         => $validated['pegawai_nip'],
            'triwulan'            => $validated['triwulan'],
            'tahapan'             => $validated['tahapan'],
            'tanggal_mulai'       => $validated['tanggal_mulai'],
            'tanggal_selesai'     => $validated['tanggal_selesai'],
            'uraian'              => $validated['uraian'],
            'penjelasan_kegiatan' => $validated['penjelasan_kegiatan'] ?? null,
            'realisasi_kegiatan'  => $validated['realisasi_kegiatan'] ?? null,
            'lampiran'            => $paths,
        ]);

        return redirect()->back()->with('success', 'Aktivitas berhasil dicatat.');
    }
}
