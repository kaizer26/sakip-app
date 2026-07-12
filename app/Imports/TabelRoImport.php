<?php

namespace App\Imports;

use App\Models\TabelRo;
use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TabelRoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $indikator = Indikator::where('kode', $row['kode_indikator'])->first();

        // Skip if indicator not found
        if (!$indikator) {
            return null;
        }

        return new TabelRo([
            'indikator_id'        => $indikator->id,
            'tahun'               => $row['tahun'] ?? date('Y'),
            'triwulan'            => $row['triwulan'] ?? 1,
            'ro'                  => $row['ro'],
            'realisasi_volume_ro' => $row['realisasi_volume_ro'] ?? 0,
            'progres_ro'          => $row['progres_ro'] ?? 0,
            'pagu_awal'           => $row['pagu_awal'] ?? 0,
            'pagu_revisi'         => $row['pagu_revisi'] ?? 0,
            'pagu_sisa'           => $row['pagu_sisa'] ?? 0,
            'pagu_realisasi'      => $row['pagu_realisasi'] ?? 0,
        ]);
    }
}
