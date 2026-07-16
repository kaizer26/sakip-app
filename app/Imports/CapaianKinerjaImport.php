<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Indikator;
use App\Models\Realisasi;
use App\Models\CapaianKinerja;
use App\Models\Analisis;

class CapaianKinerjaImport implements ToCollection, WithHeadingRow
{
    protected $tahun;
    protected $triwulan;

    public function __construct($tahun, $triwulan)
    {
        $this->tahun = $tahun;
        $this->triwulan = $triwulan;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Asumsi header excel: kode_indikator, realisasi_tw, realisasi_x, realisasi_y,
            // link_bukti_dukung_kinerja, link_bukti_dukung_rencana_tindak_lanjut_triwulan_sebelumnya
            
            $kode = trim($row['kode_indikator']);
            if (empty($kode)) continue;

            $indikator = Indikator::where('kode', $kode)->first();
            if (!$indikator) continue;

            // Proses Realisasi untuk Triwulan yang dipilih
            if (isset($row['realisasi_tw']) && is_numeric($row['realisasi_tw'])) {
                $dataRealisasi = ['realisasi_kumulatif' => $row['realisasi_tw']];
                
                if (isset($row['realisasi_x']) && is_numeric($row['realisasi_x'])) {
                    $dataRealisasi['realisasi_x'] = $row['realisasi_x'];
                }
                
                if (isset($row['realisasi_y']) && is_numeric($row['realisasi_y'])) {
                    $dataRealisasi['realisasi_y'] = $row['realisasi_y'];
                }

                Realisasi::updateOrCreate(
                    ['indikator_id' => $indikator->id, 'triwulan' => $this->triwulan],
                    $dataRealisasi
                );
            }

            // Proses Capaian (Kualitatif) untuk Triwulan yang dipilih saat upload
            // Tabel: capaian_kinerjas
            CapaianKinerja::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'tahun' => $this->tahun,
                    'triwulan' => $this->triwulan,
                ],
                [
                    'link_bukti_kinerja' => $row['link_bukti_dukung_kinerja'] ?? null,
                    'link_bukti_tindak_lanjut' => $row['link_bukti_dukung_rencana_tindak_lanjut_triwulan_sebelumnya'] ?? null,
                ]
            );
            
            // Generate Analisis default row if it doesn't exist
            Analisis::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'triwulan' => $this->triwulan,
                ],
                [
                    'severity' => 'Low',
                    'pegawai_nip' => auth()->user()->pegawai?->nip ?? auth()->user()->pegawai?->email_bps,
                ]
            );
        }
    }
}
