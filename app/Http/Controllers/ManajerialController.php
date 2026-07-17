<?php

namespace App\Http\Controllers;

use App\Models\Rtl;
use App\Models\Issue;
use App\Models\Indikator;
use Illuminate\Http\Request;

class ManajerialController extends Controller
{
    public function index()
    {
        // Require Admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $allRtls = Rtl::with('issue.indikator')->get();
        
        $selesai = $allRtls->where('status_rtl', 'Closed')->count();
        $berjalan = $allRtls->whereIn('status_rtl', ['Open', 'In Progress'])->where('due_date', '>=', today())->count();
        $terlambat = $allRtls->whereIn('status_rtl', ['Open', 'In Progress'])->where('due_date', '<', today())->count();
        $menunggu = $allRtls->where('status_rtl', 'Selesai')->count();

        // Kendala Kronis: IKU with overdue RTLs or multiple issues across triwulans
        $kronisIndikators = Indikator::whereHas('issues.rtls', function($q) {
            $q->where('due_date', '<', today())->whereIn('status_rtl', ['Open', 'In Progress']);
        })->with(['issues.rtls' => function($q) {
            $q->where('due_date', '<', today())->whereIn('status_rtl', ['Open', 'In Progress']);
        }])->get();

        // Verifikasi Atasan
        $menungguVerifikasi = Rtl::with(['issue.indikator', 'pic', 'executions'])->where('status_rtl', 'Selesai')->get();

        return view('monitoring_manajerial.index', compact('selesai', 'berjalan', 'terlambat', 'menunggu', 'kronisIndikators', 'menungguVerifikasi'));
    }

    public function verifikasi(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,revise',
        ]);

        $rtl = Rtl::findOrFail($id);

        if ($validated['action'] == 'approve') {
            $rtl->update(['status_rtl' => 'Closed']);
            // Optionally update the execution with verifier info
            if ($rtl->executions->isNotEmpty()) {
                $rtl->executions->last()->update(['verified_by' => auth()->user()->pegawai?->nip ?? auth()->user()->pegawai?->email_bps ?? auth()->user()->email]);
            }
            return redirect()->back()->with('success', 'RTL berhasil diverifikasi dan ditutup.');
        } else {
            $rtl->update(['status_rtl' => 'In Progress']); // Send back to PIC
            return redirect()->back()->with('warning', 'RTL dikembalikan untuk direvisi oleh PIC.');
        }
    }
}
