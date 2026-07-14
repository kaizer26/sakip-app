<?php

namespace App\Http\Controllers;

use App\Models\TindakLanjut;
use Illuminate\Http\Request;

class RiwayatKendalaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TindakLanjut::with(['analisis.indikator', 'analisis.pegawai']);

        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai->nip ?? $user->pegawai->email_bps ?? null;
            if (!$pegawaiNip) {
                return redirect()->back()->with('error', 'Profil pegawai Anda belum lengkap.');
            }
            $query->whereHas('analisis', function($q) use ($pegawaiNip) {
                $q->where('pegawai_nip', $pegawaiNip);
            });
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

        $tl = TindakLanjut::findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai->nip ?? $user->pegawai->email_bps ?? null;
            if ($tl->analisis->pegawai_nip !== $pegawaiNip) {
                abort(403, 'Unauthorized action.');
            }
        }

        $tl->update([
            'kendala' => $validated['kendala'],
            'solusi' => $validated['solusi'] ?? null,
            'rtl' => $validated['rencana_tindak_lanjut'] ?? null,
            'pic' => $validated['pic_tindak_lanjut'] ?? null,
            'batas_waktu' => $validated['batas_waktu'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Riwayat kendala berhasil diperbarui.');
    }
}
