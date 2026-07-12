<?php

namespace App\Exports;

use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RealisasiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Realisasi::with('indikator.target')->get();
    }

    public function headings(): array
    {
        return [
            'No', 
            'Kode Tujuan', 
            'Kode Sasaran', 
            'Kode Indikator Kinerja', 
            'Indikator Kinerja', 
            'Satuan', 'Triwulan', 
            'Target Triwulan', 'Realisasi Kumulatif', 'Capaian (%)'
        ];
    }

    public function map($realisasi): array
    {
        static $no = 1;
        $targetField = 'target_tw' . $realisasi->triwulan;
        $targetVal = $realisasi->indikator->target ? $realisasi->indikator->target->$targetField : 0;

        return [
            $no++,
            $realisasi->indikator->kode_tujuan,
            $realisasi->indikator->kode_sasaran,
            $realisasi->indikator->kode_indikator_kinerja,
            $realisasi->indikator->indikator_kinerja,
            $realisasi->indikator->satuan,
            'TW ' . $realisasi->triwulan,
            $targetVal,
            $realisasi->realisasi_kumulatif,
            number_format($realisasi->capaian_triwulan, 2) . '%',
        ];
    }
}
