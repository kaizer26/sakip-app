<?php

namespace App\Http\Controllers;

use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', \App\Models\Setting::get('default_tahun', date('Y')));
        $triwulan = $request->get('triwulan', \App\Models\Setting::get('default_triwulan', min(ceil(date('n') / 3), 4)));
        
        $user = auth()->user();
        
        $query = TindakLanjut::with(['analisis.indikator'])
            ->whereHas('analisis', function($q) use ($tahun) {
                // filtering logic
            });

        if ($triwulan) {
            $query->whereHas('analisis', function($q) use ($triwulan) {
                $q->where('triwulan', $triwulan);
            });
        }
            
        // If not admin, only show RTL assigned to this PIC
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email;
            $pegawaiName = $user->pegawai->nama ?? null;
            $query->where(function($q) use ($pegawaiName, $pegawaiNip) {
                $q->where('pic', $pegawaiName)
                  ->orWhereHas('analisis', function($q2) use ($pegawaiNip) {
                      $q2->where('pegawai_nip', $pegawaiNip);
                  });
            });
        }
        
        $tindakLanjuts = $query->latest()->get();
        
        // Summary stats
        $total = $tindakLanjuts->count();
        $selesai = $tindakLanjuts->where('status', 'Selesai')->count();
        $belumSelesai = $total - $selesai;
        
        return view('monitoring_rtl.index', compact('tindakLanjuts', 'tahun', 'triwulan', 'total', 'selesai', 'belumSelesai'));
    }

    public function markAsSelesai(Request $request, $id)
    {
        $validated = $request->validate([
            'link_bukti' => 'nullable|string',
        ]);

        $tl = TindakLanjut::findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email;
            $pegawaiName = $user->pegawai->nama ?? null;
            if ($tl->pic !== $pegawaiName && $tl->analisis->pegawai_nip !== $pegawaiNip) {
                abort(403, 'Unauthorized action.');
            }
        }

        $tl->update([
            'status' => 'Selesai',
            'link_bukti' => $validated['link_bukti'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Status RTL berhasil diubah menjadi Selesai.');
    }
}
