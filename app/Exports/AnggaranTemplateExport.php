<?php

namespace App\Exports;

use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnggaranTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $rows = [];
        $indikators = Indikator::where('tahun', $this->tahun)->orderBy('kode')->get();

        $grouped = [];
        foreach ($indikators as $ind) {
            $parts = explode('.', $ind->kode);
            if (count($parts) >= 3) {
                $sasaranKode = implode('.', array_slice($parts, 0, 3));
            } else {
                $sasaranKode = $ind->kode;
            }
            if (!isset($grouped[$sasaranKode])) {
                $grouped[$sasaranKode] = [
                    'sasaran' => $ind->sasaran,
                    'indikators' => []
                ];
            }
            $grouped[$sasaranKode]['indikators'][] = $ind;
        }

        $sasaranAnggarans = \App\Models\SasaranAnggaran::where('tahun', $this->tahun)->get()->keyBy('kode');

        foreach ($grouped as $kode => $group) {
            $sAnggaran = $sasaranAnggarans->get($kode);
            $rows[] = [
                'tahun' => $this->tahun,
                'kode' => $kode,
                'nama' => '[SASARAN] ' . $group['sasaran'],
                'pagu_awal' => $sAnggaran ? $sAnggaran->pagu_awal : 0,
                'pagu_revisi' => $sAnggaran ? $sAnggaran->pagu_revisi : 0,
                'realisasi_tw1' => $sAnggaran ? $sAnggaran->realisasi_tw1 : 0,
                'realisasi_tw2' => $sAnggaran ? $sAnggaran->realisasi_tw2 : 0,
                'realisasi_tw3' => $sAnggaran ? $sAnggaran->realisasi_tw3 : 0,
                'realisasi_tw4' => $sAnggaran ? $sAnggaran->realisasi_tw4 : 0,
            ];

            foreach ($group['indikators'] as $ind) {
                $anggaran = $ind->anggarans()->where('tahun', $this->tahun)->first();
                $rows[] = [
                    'tahun' => $this->tahun,
                    'kode' => $ind->kode,
                    'nama' => $ind->indikator_kinerja,
                    'pagu_awal' => $anggaran ? $anggaran->pagu_awal : 0,
                    'pagu_revisi' => $anggaran ? $anggaran->pagu_revisi : 0,
                    'realisasi_tw1' => $anggaran ? $anggaran->realisasi_tw1 : 0,
                    'realisasi_tw2' => $anggaran ? $anggaran->realisasi_tw2 : 0,
                    'realisasi_tw3' => $anggaran ? $anggaran->realisasi_tw3 : 0,
                    'realisasi_tw4' => $anggaran ? $anggaran->realisasi_tw4 : 0,
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'TAHUN',
            'KODE IKU',
            'INDIKATOR KINERJA',
            'PAGU AWAL',
            'PAGU REVISI',
            'REALISASI TW1',
            'REALISASI TW2',
            'REALISASI TW3',
            'REALISASI TW4',
        ];
    }

    public function map($row): array
    {
        return [
            $row['tahun'],
            $row['kode'],
            $row['nama'],
            $row['pagu_awal'],
            $row['pagu_revisi'],
            $row['realisasi_tw1'],
            $row['realisasi_tw2'],
            $row['realisasi_tw3'],
            $row['realisasi_tw4'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
