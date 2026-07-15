<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\Indikator;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', \App\Models\Setting::get('default_tahun', date('Y')));
        $triwulan = $request->get('triwulan', \App\Models\Setting::get('default_triwulan', ceil(date('n') / 3)));
        $indikator_id = $request->get('indikator_id');
        
        $query = Aktivitas::with(['indikator', 'pegawai'])
            ->whereNotNull('lampiran')
            ->where('lampiran', '!=', '[]')
            ->whereHas('indikator', function($q) use ($tahun) {
                if ($tahun) {
                    $q->where('tahun', $tahun);
                }
            });

        if ($triwulan) {
            $query->where('triwulan', $triwulan);
        }

        if ($indikator_id) {
            $query->where('indikator_id', $indikator_id);
        }

        if (!auth()->user()->isAdmin()) {
            $user = auth()->user();
            if ($user->pegawai) {
                $query->where('pegawai_nip', $user->pegawai->nip);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $evidences = $query->latest()->get();
        $indikators = Indikator::where('tahun', $tahun)->get();

        return view('admin.evidence.index', compact('evidences', 'indikators', 'tahun'));
    }
}
