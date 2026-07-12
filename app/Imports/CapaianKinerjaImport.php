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
            // Asumsi header excel: kode_indikator, realisasi_tw1, realisasi_tw2, realisasi_tw3, realisasi_tw4,
            // kendala, solusi, rencana_tindak_lanjut, pic_tindak_lanjut, batas_waktu, link_kinerja, link_rtl_sebelumnya
            
            $kode = trim($row['kode_indikator']);
            if (empty($kode)) continue;

            $indikator = Indikator::where('kode', $kode)->first();
            if (!$indikator) continue;

            // Proses Realisasi untuk Triwulan yang dipilih
            if (isset($row['realisasi_tw']) && is_numeric($row['realisasi_tw'])) {
                Realisasi::updateOrCreate(
                    ['indikator_id' => $indikator->id, 'triwulan' => $this->triwulan],
                    ['realisasi_kumulatif' => $row['realisasi_tw']]
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
                    // Penjelasan lainnya tidak disebutkan di list, kita set nullable if not provided
                ]
            );

            // Tabel: analisis
            Analisis::updateOrCreate(
                [
                    'indikator_id' => $indikator->id,
                    'triwulan' => $this->triwulan,
                ],
                [
                    'kendala' => $row['kendala_yg_dihadapi'] ?? null,
                    'solusi' => $row['solusi_yg_telah_dilakukan'] ?? null,
                    'rencana_tindak_lanjut' => $row['rencana_tindak_lanjut'] ?? null,
                    'pic_tindak_lanjut' => $row['pic_tindak_lanjut'] ?? null,
                    // Convert Excel date or use as string based on formatting
                    'batas_waktu' => (isset($row['batas_waktu_tl']) && is_numeric($row['batas_waktu_tl'])) 
                                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['batas_waktu_tl'])->format('Y-m-d') 
                                        : ($row['batas_waktu_tl'] ?? null),
                    'severity' => 'Low', // Default
                    'pegawai_nip' => auth()->user()->pegawai ? auth()->user()->pegawai->nip : null,
                ]
            );
        }
    }
}
