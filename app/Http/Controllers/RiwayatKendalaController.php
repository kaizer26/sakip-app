<?php

namespace App\Http\Controllers;

use App\Models\Analisis;
use Illuminate\Http\Request;

class RiwayatKendalaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Analisis::with(['indikator', 'pegawai'])->whereNotNull('kendala');

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
            'triwulan' => 'required|integer|between:1,4',
            'kendala' => 'required|string',
            'solusi' => 'nullable|string',
            'rencana_tindak_lanjut' => 'nullable|string',
            'pic_tindak_lanjut' => 'nullable|string',
            'batas_waktu' => 'nullable|date',
        ]);

        $analisis = Analisis::findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai->nip ?? $user->pegawai->email_bps ?? null;
            if ($analisis->pegawai_nip !== $pegawaiNip) {
                abort(403, 'Unauthorized action.');
            }
        }

        $analisis->update([
            'triwulan' => $validated['triwulan'],
            'kendala' => $validated['kendala'],
            'solusi' => $validated['solusi'] ?? null,
            'rencana_tindak_lanjut' => $validated['rencana_tindak_lanjut'] ?? null,
            'pic_tindak_lanjut' => $validated['pic_tindak_lanjut'] ?? null,
            'batas_waktu' => $validated['batas_waktu'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Riwayat kendala berhasil diperbarui.');
    }
}
