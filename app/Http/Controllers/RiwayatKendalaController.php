<?php

namespace App\Http\Controllers;

use App\Models\TindakLanjut;
use Illuminate\Http\Request;

class RiwayatKendalaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Issue::with(['indikator', 'pegawai', 'rtls']);

        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai->nip ?? $user->pegawai->email_bps ?? null;
            if (!$pegawaiNip) {
                return redirect()->back()->with('error', 'Profil pegawai Anda belum lengkap.');
            }
            $query->where('pegawai_nip', $pegawaiNip);
        }

        $riwayatKendala = $query->latest()->get();

        return view('riwayat_kendala.index', compact('riwayatKendala'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kendala' => 'required|string',
            'solusi' => 'nullable|string',
            'rencana_tindak_lanjut' => 'nullable|string',
            'pic_tindak_lanjut' => 'nullable|string',
            'batas_waktu' => 'nullable|date',
        ]);

        $tl = \App\Models\Issue::findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai->nip ?? $user->pegawai->email_bps ?? null;
            if ($tl->pegawai_nip !== $pegawaiNip) {
                abort(403, 'Unauthorized action.');
            }
        }

        $tl->update([
            'deskripsi' => $validated['kendala'],
            'solusi_sementara' => $validated['solusi'] ?? null,
        ]);
        
        $rtl = $tl->rtls()->first();
        if ($rtl || !empty($validated['rencana_tindak_lanjut'])) {
            if (!$rtl) {
                $tl->rtls()->create([
                    'deskripsi_rtl' => $validated['rencana_tindak_lanjut'] ?? '',
                    'pic_nip' => $validated['pic_tindak_lanjut'] ?? null,
                    'due_date' => $validated['batas_waktu'] ?? null,
                    'status_rtl' => 'Belum Selesai',
                ]);
            } else {
                $rtl->update([
                    'deskripsi_rtl' => $validated['rencana_tindak_lanjut'] ?? '',
                    'pic_nip' => $validated['pic_tindak_lanjut'] ?? null,
                    'due_date' => $validated['batas_waktu'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Riwayat kendala berhasil diperbarui.');
    }
}
