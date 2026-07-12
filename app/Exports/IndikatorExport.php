<?php

namespace App\Exports;

use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndikatorExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Indikator::select(
            'id', 
            'kode',
            'kode_tujuan', 'tujuan', 
            'kode_sasaran', 'sasaran', 
            'kode_indikator_kinerja', 'indikator_kinerja', 
            'jenis_indikator', 'satuan', 'target_tahunan', 'tahun'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID', 
            'Kode',
            'Kode Tujuan', 'Tujuan', 
            'Kode Sasaran', 'Sasaran', 
            'Kode Indikator Kinerja', 'Indikator Kinerja', 
            'Jenis Indikator', 'Satuan', 'Target Tahunan', 'Tahun'
        ];
    }
}
