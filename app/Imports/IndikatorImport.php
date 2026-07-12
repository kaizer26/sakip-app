<?php

namespace App\Imports;

use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndikatorImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Indikator([
            'kode'                   => $row['kode'],
            'kode_tujuan'            => $row['kode_tujuan'] ?? null,
            'tujuan'                 => $row['tujuan'],
            'kode_sasaran'           => $row['kode_sasaran'] ?? null,
            'sasaran'                => $row['sasaran'],
            'kode_indikator_kinerja' => $row['kode_indikator_kinerja'] ?? null,
            'indikator_kinerja'      => $row['indikator_kinerja'],
            'jenis_indikator'        => $row['jenis_indikator'],
            'periode'                => $row['periode'],
            'tipe'                   => $row['tipe'],
            'satuan'                 => $row['satuan'],
            'target_tahunan'         => $row['target_tahunan'] ?? 0,
            'tahun'                  => $row['tahun'] ?? 2026,
        ]);
    }
}
