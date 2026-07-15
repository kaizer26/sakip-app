<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Realisasi;
use Illuminate\Http\Request;

class CapaianController extends Controller
{
    public function rekap(Request $request)
    {
        $tahun = $request->get('tahun', \App\Models\Setting::get('default_tahun', date('Y')));
        
        $indicators = Indikator::with(['target', 'realisasis', 'kegiatanMasters', 'analisis', 'issues'])
            ->where('tahun', $tahun)
            ->get();

        // Grouping logic: Tujuan -> Sasaran -> Indikator
        $grouped = $indicators->groupBy('tujuan')->map(function ($itemsByTujuan) {
            return $itemsByTujuan->groupBy('sasaran');
        });

        return view('capaian.rekap', compact('grouped', 'tahun'));
    }

    public function export(Request $request)
    {
        $tahun = $request->get('tahun', \App\Models\Setting::get('default_tahun', date('Y')));
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CapaianExport($tahun), 
            "Rekap_Capaian_Kinerja_{$tahun}.xlsx"
        );
    }
}
