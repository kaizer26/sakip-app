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
        $batasWaktu = $this->tahun . '-12-31';

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


            // Parse text to arrays
            $kendalas = $this->parseBulletPoints($row['kendala_yg_dihadapi'] ?? '');
            $solusis = $this->parseBulletPoints($row['solusi_yg_telah_dilakukan'] ?? '');
            $rtls = $this->parseBulletPoints($row['rencana_tindak_lanjut'] ?? '');

            if (count($kendalas) > 0 || count($solusis) > 0 || count($rtls) > 0) {
                // Hapus data lama agar tidak dobel saat re-import
                $oldIssues = \App\Models\Issue::where('indikator_id', $indikator->id)
                    ->where('triwulan', $this->triwulan)
                    ->where('tahun', $this->tahun)
                    ->get();
                foreach ($oldIssues as $oi) {
                    $oi->rtls()->delete();
                    $oi->delete();
                }

                $maxCount = max(count($kendalas), count($solusis), count($rtls));
                for ($i = 0; $i < $maxCount; $i++) {
                    $issue = \App\Models\Issue::create([
                        'indikator_id' => $indikator->id,
                        'triwulan' => $this->triwulan,
                        'tahun' => $this->tahun,
                        'status_kendala' => 'Belum Ditangani',
                        'deskripsi' => $kendalas[$i] ?? '-',
                        'solusi_sementara' => $solusis[$i] ?? null,
                        'pegawai_nip' => auth()->user()->pegawai ? auth()->user()->pegawai->nip : '-',
                    ]);
                    
                    if (!empty($rtls[$i])) {
                        \App\Models\Rtl::create([
                            'issue_id' => $issue->id,
                            'deskripsi_rtl' => $rtls[$i],
                            'pic_nip' => $row['pic_tindak_lanjut'] ?? null,
                            'due_date' => $batasWaktu,
                            'status_rtl' => 'Open',
                        ]);
                    }
                }
            }

        }
    }

    private function parseBulletPoints($text)
    {
        if (empty($text)) return [];
        
        $lines = explode("\n", $text);
        $points = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Hapus karakter bullet atau penomoran di awal string
            $line = preg_replace('/^[-•*\s]+/', '', $line);
            $line = preg_replace('/^\d+[\.)]\s*/', '', $line);
            
            if (!empty($line)) {
                $points[] = $line;
            }
        }
        
        // Kalau setelah dipecah baris tidak ada hasilnya
        if (count($points) === 0) {
            $points[] = trim($text);
        }
        
        return $points;
    }
}
