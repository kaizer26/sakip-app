<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $triwulan = $request->get('triwulan', 1);
        $user = auth()->user();
        $pegawai_id = $user->pegawai_id;
        
        $indikators = Indikator::where('tahun', $tahun)->with(['target', 'realisasis'])->get();

        // Common reporting indicators logic (PIC OR member/leader)
        $reportingIndikators = collect();
        if ($pegawai_id) {
            $reportingIndikators = Indikator::where('tahun', $tahun)
                ->where(function($q) use ($pegawai_id) {
                    $q->where('pic_id', $pegawai_id)
                      ->orWhereHas('kegiatanMasters', function($q2) use ($pegawai_id) {
                          $q2->where('ketua_tim_id', $pegawai_id)
                             ->orWhereHas('anggotas', function($q3) use ($pegawai_id) {
                                 $q3->where('pegawai_id', $pegawai_id);
                             });
                      });
                })->get();
        }

        if ($user->isAdmin()) {
            $summary = [
                'total' => $indikators->count(),
                'hijau' => $indikators->filter(fn($i) => $i->status_warna == 'success')->count(),
                'kuning' => $indikators->filter(fn($i) => $i->status_warna == 'warning')->count(),
                'merah' => $indikators->filter(fn($i) => $i->status_warna == 'danger')->count(),
            ];
            
            return view('dashboard', [
                'indikators' => $indikators,
                'reportingIndikators' => $reportingIndikators,
                'summary' => $summary,
                'tahun' => $tahun,
                'triwulan' => $triwulan
            ]);
        } else {
            // Pegawai View
            $pegawai = $user->pegawai;
            
            $myActivitiesCount = 0;
            $myIndicators = collect();
            
            if ($pegawai) {
                $myActivitiesCount = \App\Models\Aktivitas::where('pegawai_nip', $pegawai->nip)->count();
                
                // Indicators where user is PIC (for the table)
                $myIndicators = Indikator::where('pic_id', $pegawai->id)
                    ->where('tahun', $tahun)
                    ->with(['target', 'realisasis'])
                    ->get();
                    
                $pegawais = \App\Models\Pegawai::orderBy('nama')->get();
            }

            $summary = [
                'personal_activities' => $myActivitiesCount,
                'total_pic' => $myIndicators->count(),
                'pic_hijau' => $myIndicators->filter(fn($i) => $i->status_warna == 'success')->count(),
                'pic_critical' => $myIndicators->filter(fn($i) => $i->status_warna == 'danger')->count(),
            ];

            return view('dashboard_pegawai', [
                'indikators' => $myIndicators,
                'reportingIndikators' => $reportingIndikators,
                'summary' => $summary,
                'tahun' => $tahun,
                'triwulan' => $triwulan,
                'pegawai' => $pegawai,
                'pegawais' => $pegawais ?? collect(),
                'error' => !$pegawai ? 'Data profil pegawai Anda belum ditautkan oleh Admin.' : null
            ]);
        }
    }
}
