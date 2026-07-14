<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class EvaluasiKinerjaController extends Controller
{
    public function index(Request $request)
    {
        $defaultTahun = \App\Models\Setting::get('default_tahun', date('Y'));
        $defaultTriwulan = \App\Models\Setting::get('default_triwulan', ceil(date('n') / 3));

        $tahun = $request->get('tahun', $defaultTahun);
        $triwulan = $request->get('triwulan', $defaultTriwulan);

        $user = auth()->user();
        
        $indikators = Indikator::visibleTo($user)
            ->where('tahun', $tahun)
            ->with(['realisasis' => function($q) use ($triwulan) {
                $q->where('triwulan', $triwulan);
            }, 'issues' => function($q) use ($triwulan, $tahun) {
                $q->where('triwulan', $triwulan)->where('tahun', $tahun);
            }])
            ->get();
            
        $pegawais = Pegawai::all();

        return view('evaluasi_kinerja.index', compact('indikators', 'tahun', 'triwulan', 'pegawais'));
    }
}
