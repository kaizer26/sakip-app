<?php

namespace App\Http\Controllers;

use App\Models\Rtl;
use App\Models\RtlExecution;
use Illuminate\Http\Request;

class RtlController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $tab = $request->get('tab', 'semua');
        
        $query = Rtl::with(['issue.indikator']);

        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email;
            $query->where('pic_nip', $pegawaiNip);
        }

        // Apply tab filters
        if ($tab == 'overdue') {
            $query->where('due_date', '<', today())->whereIn('status_rtl', ['Open', 'In Progress']);
        } elseif ($tab == 'berjalan') {
            $query->whereIn('status_rtl', ['Open', 'In Progress'])->where('due_date', '>=', today());
        } elseif ($tab == 'menunggu') {
            $query->where('status_rtl', 'Selesai'); // Waiting for verification to become Closed
        } elseif ($tab == 'selesai') {
            $query->where('status_rtl', 'Closed');
        }

        $rtls = $query->orderBy('due_date', 'asc')->get();

        // Counts for tabs
        $baseQuery = Rtl::query();
        if (!$user->isAdmin()) {
            $baseQuery->where('pic_nip', $user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email);
        }
        $counts = [
            'semua' => (clone $baseQuery)->count(),
            'overdue' => (clone $baseQuery)->where('due_date', '<', today())->whereIn('status_rtl', ['Open', 'In Progress'])->count(),
            'berjalan' => (clone $baseQuery)->whereIn('status_rtl', ['Open', 'In Progress'])->where('due_date', '>=', today())->count(),
            'menunggu' => (clone $baseQuery)->where('status_rtl', 'Selesai')->count(),
            'selesai' => (clone $baseQuery)->where('status_rtl', 'Closed')->count(),
        ];

        return view('monitoring_rtl.index', compact('rtls', 'tab', 'counts'));
    }

    public function storeExecution(Request $request, $id)
    {
        $validated = $request->validate([
            'catatan_progres' => 'required|string',
            'file_bukti_dukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $rtl = Rtl::findOrFail($id);
        
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pegawaiNip = $user->pegawai?->nip ?? $user->pegawai?->email_bps ?? $user->email;
            if ($rtl->pic_nip !== $pegawaiNip) {
                abort(403, 'Unauthorized action.');
            }
        }

        $filePath = null;
        if ($request->hasFile('file_bukti_dukung')) {
            $filePath = $request->file('file_bukti_dukung')->store('bukti_rtl', 'public');
        }

        RtlExecution::create([
            'rtl_id' => $rtl->id,
            'triwulan' => ceil(date('n') / 3),
            'tahun' => date('Y'),
            'catatan_progres' => $validated['catatan_progres'],
            'file_bukti_dukung' => $filePath,
        ]);

        // Change status to Selesai (Waiting for Verification)
        $rtl->update(['status_rtl' => 'Selesai']);

        return redirect()->back()->with('success', 'Eksekusi RTL berhasil dikirim untuk diverifikasi.');
    }
}
