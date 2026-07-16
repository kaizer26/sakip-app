<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\Indikator;
use App\Models\IndikatorAnggaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class AnggaranImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function model(array $row)
    {
        if (empty($row['kode_iku']) || empty($row['tahun'])) {
            return null;
        }

        $kodeParts = explode('.', $row['kode_iku']);
        
        // 3-digit = SasaranAnggaran
        if (count($kodeParts) === 3) {
            return \App\Models\SasaranAnggaran::updateOrCreate(
                [
                    'kode' => $row['kode_iku'],
                    'tahun' => $row['tahun'],
                ],
                [
                    'pagu_awal' => $row['pagu_awal'] ?? 0,
                    'pagu_revisi' => $row['pagu_revisi'] ?? 0,
                    'realisasi_tw1' => $row['realisasi_tw1'] ?? 0,
                    'realisasi_tw2' => $row['realisasi_tw2'] ?? 0,
                    'realisasi_tw3' => $row['realisasi_tw3'] ?? 0,
                    'realisasi_tw4' => $row['realisasi_tw4'] ?? 0,
                    'kode_kegiatan' => $row['kode_kegiatan'] ?? null,
                    'nama_kegiatan' => $row['nama_kegiatan'] ?? null,
                    'kode_ro' => $row['kode_ro'] ?? null,
                    'nama_ro' => $row['nama_ro'] ?? null,
                ]
            );
        }

        // 4-digit = IndikatorAnggaran
        $indikator = Indikator::where('kode', $row['kode_iku'])
            ->where('tahun', $row['tahun'])
            ->first();

        if ($indikator) {
            return IndikatorAnggaran::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'tahun' => $row['tahun'],
                ],
                [
                    'pagu_awal' => $row['pagu_awal'] ?? 0,
                    'pagu_revisi' => $row['pagu_revisi'] ?? 0,
                    'realisasi_tw1' => $row['realisasi_tw1'] ?? 0,
                    'realisasi_tw2' => $row['realisasi_tw2'] ?? 0,
                    'realisasi_tw3' => $row['realisasi_tw3'] ?? 0,
                    'realisasi_tw4' => $row['realisasi_tw4'] ?? 0,
                    'kode_kegiatan' => $row['kode_kegiatan'] ?? null,
                    'nama_kegiatan' => $row['nama_kegiatan'] ?? null,
                    'kode_ro' => $row['kode_ro'] ?? null,
                    'nama_ro' => $row['nama_ro'] ?? null,
                ]
            );
        }

        return null;
    }
}
