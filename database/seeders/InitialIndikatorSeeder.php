<?php

namespace Database\Seeders;

use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialIndikatorSeeder extends Seeder
{
    public function run(): void
    {
        // Mapping full descriptions for the seeder
        $realData = [
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas",
                "kode_sasaran" => "1.1.1",
                "indikator" => "Persentase Publikasi/Laporan Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas",
                "kode_indikator" => "1.1.1.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Kesejahteraan Rakyat yang Berkualitas",
                "kode_sasaran" => "1.1.3",
                "indikator" => "Persentase Publikasi/Laporan Statistik Kesejahteraan Rakyat yang Berkualitas",
                "ikode_ndikator" => "1.1.3.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya penyediaan Data dan Insight Statistik Ketahanan Sosial yang Berkualitas",
                "kode_sasaran" => "1.1.5",
                "indikator" => "Persentase Publikasi/Laporan Statistik Ketahanan Sosial yang Berkualitas",
                "ikode_ndikator" => "1.1.5.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Sumber Daya Hayati yang Berkualitas",
                "kode_sasaran" => "1.2.1",
                "indikator" => "Persentase Publikasi/Laporan Statistik Sumber Daya Hayati yang Berkualitas",
                "ikode_ndikator" => "1.2.1.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Sumber Daya Mineral dan Konstruksi yang Berkualitas",
                "kode_sasaran" => "1.2.2",
                "indikator" => "Persentase publikasi/laporan Statistik Sumber Daya Mineral dan Konstruksi yang Berkualitas",
                "ikode_ndikator" => "1.2.2.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya penyediaan Data dan Insight Statistik Industri yang Berkualitas",
                "kode_sasaran" => "1.2.3",
                "indikator" => "Persentase publikasi/laporan Statistik Industri yang Berkualitas",
                "ikode_ndikator" => "1.2.3.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Distribusi yang Berkualitas",
                "kode_sasaran" => "1.3.1",
                "indikator" => "Persentase Publikasi/Laporan Statistik Distribusi yang Berkualitas",
                "ikode_ndikator" => "1.3.1.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Harga yang Berkualitas",
                "kode_sasaran" => "1.3.3",
                "indikator" => "Persentase Publikasi/laporan Statistik Harga yang Berkualitas",
                "ikode_ndikator" => "1.3.3.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Jasa yang Berkualitas",
                "kode_sasaran" => "1.3.5",
                "indikator" => "Persentase Publikasi/Laporan Statistik Jasa yang Berkualitas",
                "ikode_ndikator" => "1.3.5.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Lintas Sektor yang Berkualitas",
                "kode_sasaran" => "1.4.1",
                "indikator" => "Persentase Publikasi/Laporan Neraca Produksi yang Berkualitas",
                "ikode_ndikator" => "1.4.1.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Lintas Sektor yang Berkualitas",
                "kode_sasaran" => "1.4.1",
                "indikator" => "Persentase Publikasi/Laporan Neraca Pengeluaran yang Berkualitas",
                "ikode_ndikator" => "1.4.1.2",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Perumusan Kebijakan dan Pengambilan Keputusan Berbasis Data Statistik Berkualitas dan Insight yang Relevan",
                "kode_tujuan" => "T1",
                "sasaran" => "Terwujudnya Penyediaan Data dan Insight Statistik Lintas Sektor yang Berkualitas",
                "kode_sasaran" => "1.4.1",
                "indikator" => "Persentase Publikasi/Laporan Analisis Statistik dan Neraca Satelit yang Berkualitas",
                "ikode_ndikator" => "1.4.1.3",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Penyelenggaraan Sistem Statistik Nasional yang Andal, Efektif, dan Efisien",
                "kode_tujuan" => "T2",
                "sasaran" => "Terwujudnya Kapasitas Tata Kelola Pemerintah Desa Untuk Menghasilkan Statistik Berkualitas",
                "kode_sasaran" => "2.1.4",
                "indikator" => "Persentase Kumulatif Desa Yang Berpredikat Desa Cinta Statistik",
                "ikode_ndikator" => "2.1.4.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 4.44,
                "tw1" => 2.22,
                "tw2" => 2.22,
                "tw3" => 2.22,
                "tw4" => 4.44
            ],
            [
                "tujuan" => "Mewujudkan Penyelenggaraan Sistem Statistik Nasional yang Andal, Efektif, dan Efisien",
                "kode_tujuan" => "T2",
                "sasaran" => "Terwujudnya Penguatan Penyelenggaraan Pembinaan Statistik Sektoral Kementerian/Lembaga/Pemerintah Daerah",
                "kode_sasaran" => "2.5.1",
                "indikator" => "Tingkat Penyelenggaraan Pembinaan Statistik Sektoral sesuai standar",
                "ikode_ndikator" => "2.5.1.1",
                "jenis" => "IKU",
                "periode" => "Triwulanan",
                "satuan" => "Persen",
                "target" => 100,
                "tw1" => 19.2,
                "tw2" => 55.64,
                "tw3" => 71.7,
                "tw4" => 100
            ],
            [
                "tujuan" => "Mewujudkan Penyelenggaraan Sistem Statistik Nasional yang Andal, Efektif, dan Efisien",
                "kode_tujuan" => "T2",
                "sasaran" => "Terwujudnya Kemudahan Akses Data BPS",
                "kode_sasaran" => "2.7.1",
                "indikator" => "Indeks Pelayanan Publik - Penilaian mandiri",
                "ikode_ndikator" => "2.7.1.1",
                "jenis" => "IKU",
                "periode" => "Triwulanan",
                "satuan" => "Poin",
                "target" => 4.46,
                "tw1" => 0,
                "tw2" => 1.07,
                "tw3" => 4.15,
                "tw4" => 4.46
            ],
            [
                "tujuan" => "Mewujudkan Tata Kelola Badan Pusat Statistik yang Berkualitas, Akuntabel, Efektif, dan Efisien dalam Menyelenggarakan Statistik",
                "kode_tujuan" => "T3",
                "sasaran" => "Tersedianya Dukungan Manajemen pada BPS Provinsi dan BPS Kabupaten/Kota",
                "kode_sasaran" => "3.2.4",
                "indikator" => "Nilai SAKIP oleh Inspektorat",
                "ikode_ndikator" => "3.2.4.1",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Poin",
                "target" => 74.75,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 74.75
            ],
            [
                "tujuan" => "Mewujudkan Tata Kelola Badan Pusat Statistik yang Berkualitas, Akuntabel, Efektif, dan Efisien dalam Menyelenggarakan Statistik",
                "kode_tujuan" => "T3",
                "sasaran" => "Tersedianya Dukungan Manajemen pada BPS Provinsi dan BPS Kabupaten/Kota",
                "kode_sasaran" => "3.2.4",
                "indikator" => "Indeks Implementasi BerAKHLAK",
                "ikode_ndikator" => "3.2.4.2",
                "jenis" => "IKU",
                "periode" => "Tahunan",
                "satuan" => "Persen",
                "target" => 73,
                "tw1" => 0,
                "tw2" => 0,
                "tw3" => 0,
                "tw4" => 73
            ],
        ];

        foreach ($realData as $item) {
            $kodeTujuan = $item['kode_tujuan'] ?? null;
            $kodeSasaran = $item['kode_sasaran'] ?? null;
            $kodeIndikator = $item['kode_indikator'] ?? $item['ikode_ndikator'] ?? null;

            $indikator = Indikator::create([
                'kode' => $kodeIndikator,
                'kode_tujuan' => $kodeTujuan,
                'kode_sasaran' => $kodeSasaran,
                'kode_indikator_kinerja' => $kodeIndikator,
                'tujuan' => $item['tujuan'],
                'sasaran' => $item['sasaran'],
                'indikator_kinerja' => $item['indikator'],
                'jenis_indikator' => $item['jenis'],
                'periode' => $item['periode'] == 'Triwulanan' ? 'Triwulanan' : 'Tahunan',
                'tipe' => $item['satuan'] == 'Persen' ? 'Persen' : 'Non Persen',
                'satuan' => $item['satuan'],
                'target_tahunan' => $item['target'],
                'tahun' => 2026,
            ]);

            Target::create([
                'indikator_id' => $indikator->id,
                'target_tw1' => $item['tw1'],
                'target_tw2' => $item['tw2'],
                'target_tw3' => $item['tw3'],
                'target_tw4' => $item['tw4'],
            ]);
        }
    }
}
