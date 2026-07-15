<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Pegawai;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplateWordController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::orderBy('nama', 'asc')->get();
        return view('template_word.index', compact('pegawais'));
    }

    public function exportNotulenCapaian(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer',
            'triwulan' => 'required|integer|between:1,4',
            'tanggal' => 'required|date',
            'waktu' => 'required|string',
            'tempat' => 'required|string',
            'pimpinan_id' => 'required|exists:pegawais,id',
            'notulis_id' => 'required|exists:pegawais,id'
        ]);

        $pimpinan = Pegawai::find($validated['pimpinan_id']);
        $notulis = Pegawai::find($validated['notulis_id']);

        // Format tanggal (dddd, dd mmmm yyyy)
        \Carbon\Carbon::setLocale('id');
        $date = \Carbon\Carbon::parse($validated['tanggal']);
        $formattedDate = $date->translatedFormat('l, d F Y');

        $templatePath = storage_path('app/templates/notulen_capkin.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Notulen Capaian (notulen_capkin.docx) tidak ditemukan di folder storage/app/templates.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace tag umum
        $triwulans = ['', 'TRIWULAN I', 'TRIWULAN II', 'TRIWULAN III', 'TRIWULAN IV'];
        $triwulans1 = ['', 'Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV'];
        $templateProcessor->setValue('triwulan_upper', $triwulans[$validated['triwulan']]);
        $templateProcessor->setValue('triwulan_proper', $triwulans1[$validated['triwulan']]);
        $templateProcessor->setValue('tahun', $validated['tahun']);
        $templateProcessor->setValue('triwulan', $validated['triwulan']);
        $templateProcessor->setValue('hari_tanggal', $formattedDate);
        $templateProcessor->setValue('tanggal_notula', $formattedDate);
        $templateProcessor->setValue('waktu', $validated['waktu']);
        $templateProcessor->setValue('tempat', $validated['tempat']);
        $templateProcessor->setValue('pimpinan_rapat', $pimpinan->nama);
        $templateProcessor->setValue('jabatan_pimpinan', $pimpinan->jabatan ?? '-');
        $templateProcessor->setValue('kepala', $pimpinan->nama);
        $templateProcessor->setValue('notulis', $notulis->nama);
        $indikators = Indikator::with([
            'target',
            'realisasis' => function ($q) use ($validated) {
                $q->where('triwulan', $validated['triwulan']);
            },
            'analisis' => function ($q) use ($validated) {
                $q->where('triwulan', $validated['triwulan']);
            },
            'tabelRos' => function ($q) use ($validated) {
                $q->where('tahun', $validated['tahun'])->where('triwulan', $validated['triwulan']);
            }
        ])->get();

        $globalPaguAwal = 0;
        $globalPaguRevisi = 0;
        $globalPaguRealisasi = 0;
        foreach ($indikators as $ind) {
            $globalPaguAwal += $ind->tabelRos->sum('pagu_awal');
            $globalPaguRevisi += $ind->tabelRos->sum('pagu_revisi');
            $globalPaguRealisasi += $ind->tabelRos->sum('pagu_realisasi');
        }

        $templateProcessor->setValue('pagu_awal', number_format($globalPaguAwal, 0, ',', '.'));
        $templateProcessor->setValue('pagu_revisi', number_format($globalPaguRevisi, 0, ',', '.'));
        $templateProcessor->setValue('pagu_realisasi', number_format($globalPaguRealisasi, 0, ',', '.'));

        $templateProcessor->cloneBlock('block_indikator', count($indikators), true, true);

        foreach ($indikators as $index => $indikator) {
            $blockIdx = $index + 1;

            $realisasi = $indikator->realisasis->first();
            $analisis = $indikator->analisis->first();

            $targetField = 'target_tw' . $validated['triwulan'];
            $target = $indikator->target ? $indikator->target->$targetField : '-';

            $templateProcessor->setValue("no#{$blockIdx}", $blockIdx);
            $templateProcessor->setValue("kode_indikator#{$blockIdx}", $indikator->kode);
            $templateProcessor->setValue("kode#{$blockIdx}", $indikator->kode);
            $templateProcessor->setValue("nama_indikator#{$blockIdx}", $indikator->indikator_kinerja);
            $templateProcessor->setValue("indikator_kinerja#{$blockIdx}", $indikator->indikator_kinerja);
            $templateProcessor->setValue("indikator_kinerja_tanpa_satuan#{$blockIdx}", $indikator->indikator_kinerja);
            $templateProcessor->setValue("tujuan#{$blockIdx}", $indikator->tujuan ?? '-');
            $templateProcessor->setValue("sasaran#{$blockIdx}", $indikator->sasaran ?? '-');
            $templateProcessor->setValue("satuan#{$blockIdx}", $indikator->satuan ?? '-');
            $templateProcessor->setValue("target#{$blockIdx}", $target);
            $templateProcessor->setValue("target_tw#{$blockIdx}", $target);
            $templateProcessor->setValue("target_tahunan#{$blockIdx}", $indikator->target_tahunan ?? '-');

            $templateProcessor->setValue("definisi_x#{$blockIdx}", $indikator->definisi_x ?? '-');
            $templateProcessor->setValue("definisi_y#{$blockIdx}", $indikator->definisi_y ?? '-');

            $real_x = $realisasi ? $realisasi->realisasi_x : '-';
            $real_y = $realisasi ? $realisasi->realisasi_y : '-';
            $capaian = $realisasi ? $realisasi->realisasi_kumulatif : 0;

            $targetTahunan = $indikator->target_tahunan ?? 0;
            $capaian_triwulan = (is_numeric($target) && $target > 0) ? round(($capaian / $target) * 100, 2) : 0;
            $capaian_tahunan = (is_numeric($targetTahunan) && $targetTahunan > 0) ? round(($capaian / $targetTahunan) * 100, 2) : 0;

            $templateProcessor->setValue("real_x#{$blockIdx}", $real_x);
            $templateProcessor->setValue("x#{$blockIdx}", $real_x);
            $templateProcessor->setValue("real_y#{$blockIdx}", $real_y);
            $templateProcessor->setValue("y#{$blockIdx}", $real_y);
            $templateProcessor->setValue("capaian#{$blockIdx}", $capaian);
            $templateProcessor->setValue("realisasi_kumulatif#{$blockIdx}", $capaian);
            $templateProcessor->setValue("capaian_triwulan#{$blockIdx}", $capaian_triwulan);
            $templateProcessor->setValue("capaian_tahunan#{$blockIdx}", $capaian_tahunan);

            $kendala = $analisis ? html_entity_decode(strip_tags($analisis->kendala)) : '-';
            $solusi = $analisis ? html_entity_decode(strip_tags($analisis->solusi)) : '-';
            $tindakLanjut = $indikator->rencana_tindak_lanjut ?? '-';

            $templateProcessor->setValue("kendala#{$blockIdx}", $kendala);
            $templateProcessor->setValue("solusi#{$blockIdx}", $solusi);
            $templateProcessor->setValue("tindak_lanjut#{$blockIdx}", $tindakLanjut);
            $templateProcessor->setValue("rencana_tindak_lanjut#{$blockIdx}", $tindakLanjut);
            $templateProcessor->setValue("pic_tindak_lanjut#{$blockIdx}", $indikator->pic_tindak_lanjut ?? '-');
            $templateProcessor->setValue("batas_waktu#{$blockIdx}", $indikator->batas_waktu ?? '-');

            $basisData = $indikator->basis_data ? html_entity_decode(strip_tags($indikator->basis_data)) : '-';
            $templateProcessor->setValue("basis_data#{$blockIdx}", $basisData);
            $templateProcessor->setValue("basis_data_baseline#{$blockIdx}", $basisData);
            $templateProcessor->setValue("dasar_hitung#{$blockIdx}", $indikator->dasar_hitung ? html_entity_decode(strip_tags($indikator->dasar_hitung)) : '-');
            $templateProcessor->setValue("link_bukti_kinerja#{$blockIdx}", $indikator->link_bukti_kinerja ?? '-');
            $templateProcessor->setValue("link_bukti_tindak_lanjut#{$blockIdx}", $indikator->link_bukti_tindak_lanjut ?? '-');
            $templateProcessor->setValue("penjelasan_lainnya#{$blockIdx}", $indikator->penjelasan_lainnya ?? '-');

            // Target X/Y
            $targetObj = $indikator->target;
            for ($i = 1; $i <= 4; $i++) {
                $txField = "target_x_tw{$i}";
                $tyField = "target_y_tw{$i}";
                $templateProcessor->setValue("target_x_tw{$i}#{$blockIdx}", $targetObj ? $targetObj->$txField : '-');
                $templateProcessor->setValue("target_y_tw{$i}#{$blockIdx}", $targetObj ? $targetObj->$tyField : '-');
            }

            // Rincian Output (Tabel RO via cloneRow)
            $tabelRos = $indikator->tabelRos;
            $roCloned = false;
            try {
                $templateProcessor->cloneRow("ro#{$blockIdx}", count($tabelRos) > 0 ? count($tabelRos) : 1);
                $roCloned = true;
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                // Ignore cloneRow failure, we will replace the raw block tags with '-'
            }

            if ($roCloned) {
                if (count($tabelRos) > 0) {
                    foreach ($tabelRos as $outIndex => $tRo) {
                        $rowIdx = $outIndex + 1;

                        $templateProcessor->setValue("no_ro#{$blockIdx}#{$rowIdx}", $rowIdx . '.');
                        $templateProcessor->setValue("ro#{$blockIdx}#{$rowIdx}", $tRo->ro);
                        $templateProcessor->setValue("realisasi_volume_ro#{$blockIdx}#{$rowIdx}", $tRo->realisasi_volume_ro);
                        $templateProcessor->setValue("progres_ro#{$blockIdx}#{$rowIdx}", $tRo->progres_ro);
                        $templateProcessor->setValue("pagu_awal#{$blockIdx}#{$rowIdx}", number_format($tRo->pagu_awal, 0, ',', '.'));
                        $templateProcessor->setValue("pagu_revisi#{$blockIdx}#{$rowIdx}", number_format($tRo->pagu_revisi, 0, ',', '.'));
                        $templateProcessor->setValue("pagu_sisa#{$blockIdx}#{$rowIdx}", number_format($tRo->pagu_sisa, 0, ',', '.'));
                        $templateProcessor->setValue("pagu_realisasi#{$blockIdx}#{$rowIdx}", number_format($tRo->pagu_realisasi, 0, ',', '.'));
                    }
                } else {
                    $templateProcessor->setValue("no_ro#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("ro#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("realisasi_volume_ro#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("progres_ro#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("pagu_awal#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("pagu_revisi#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("pagu_sisa#{$blockIdx}#1", '-');
                    $templateProcessor->setValue("pagu_realisasi#{$blockIdx}#1", '-');
                }
            } else {
                // If cloneRow failed, the tags still exist without the row suffix
                $templateProcessor->setValue("no_ro#{$blockIdx}", '-');
                $templateProcessor->setValue("ro#{$blockIdx}", '-');
                $templateProcessor->setValue("realisasi_volume_ro#{$blockIdx}", '-');
                $templateProcessor->setValue("progres_ro#{$blockIdx}", '-');
                $templateProcessor->setValue("pagu_awal#{$blockIdx}", '-');
                $templateProcessor->setValue("pagu_revisi#{$blockIdx}", '-');
                $templateProcessor->setValue("pagu_sisa#{$blockIdx}", '-');
                $templateProcessor->setValue("pagu_realisasi#{$blockIdx}", '-');
            }
        }

        // Efisiensi Table (Clone Row for sasaran outside the block_indikator)
        $sasarans = $indikators->groupBy('sasaran')->filter(function ($value, $key) {
            return !empty($key);
        });

        if ($sasarans->count() > 0) {
            $sasaranCloned = false;
            try {
                $templateProcessor->cloneRow('sasaran', $sasarans->count());
                $sasaranCloned = true;
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                // Ignore cloneRow failure
            }

            if ($sasaranCloned) {
                $idx = 1;
                foreach ($sasarans as $sasaranName => $inds) {
                    $sumAwal = 0;
                    $sumRevisi = 0;
                    $sumRealisasi = 0;
                    foreach ($inds as $ind) {
                        $sumAwal += $ind->tabelRos->sum('pagu_awal');
                        $sumRevisi += $ind->tabelRos->sum('pagu_revisi');
                        $sumRealisasi += $ind->tabelRos->sum('pagu_realisasi');
                    }

                    $templateProcessor->setValue("sasaran#{$idx}", $sasaranName);
                    $templateProcessor->setValue("pagu_awal_sasaran#{$idx}", number_format($sumAwal, 0, ',', '.'));
                    $templateProcessor->setValue("pagu_revisi_sasaran#{$idx}", number_format($sumRevisi, 0, ',', '.'));
                    $templateProcessor->setValue("pagu_realisasi_sasaran#{$idx}", number_format($sumRealisasi, 0, ',', '.'));
                    $idx++;
                }
            } else {
                $templateProcessor->setValue('sasaran', '-');
                $templateProcessor->setValue('pagu_awal_sasaran', '-');
                $templateProcessor->setValue('pagu_revisi_sasaran', '-');
                $templateProcessor->setValue('pagu_realisasi_sasaran', '-');
            }
        } else {
            $templateProcessor->setValue('sasaran', '-');
            $templateProcessor->setValue('pagu_awal_sasaran', '-');
            $templateProcessor->setValue('pagu_revisi_sasaran', '-');
            $templateProcessor->setValue('pagu_realisasi_sasaran', '-');
        }

        $fileName = 'Notulen_Capaian_TW' . $validated['triwulan'] . '_' . $validated['tahun'] . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function exportSuratUndangan(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string',
            'sifat_surat' => 'required|string',
            'lampiran' => 'required|string',
            'perihal' => 'required|string',
            'tgl_surat' => 'required|date',
            'undangan' => 'required|string',
            'isi_undangan' => 'required|string',
            'hari_tanggal_kegiatan' => 'required|date',
            'waktu_kegiatan' => 'required|string',
            'tempat_kegiatan' => 'required|string',
            'agend_kegiatan' => 'required|string', // typo in user template
        ]);

        \Carbon\Carbon::setLocale('id');
        $tgl_surat = \Carbon\Carbon::parse($validated['tgl_surat'])->translatedFormat('d F Y');
        $hari_tanggal_kegiatan = \Carbon\Carbon::parse($validated['hari_tanggal_kegiatan'])->translatedFormat('l, d F Y');

        $templatePath = storage_path('app/templates/surat_undangan_rapat.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Surat Undangan (surat_undangan_rapat.docx) tidak ditemukan di folder storage/app/templates.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Standard string replaces
        $templateProcessor->setValue('nomor_surat', htmlspecialchars($validated['nomor_surat']));
        $templateProcessor->setValue('sifat_surat', htmlspecialchars($validated['sifat_surat']));
        $templateProcessor->setValue('lampiran', htmlspecialchars($validated['lampiran']));
        $templateProcessor->setValue('perihal', htmlspecialchars($validated['perihal']));
        $templateProcessor->setValue('tgl_surat', $tgl_surat);
        $templateProcessor->setValue('hari_tanggal_kegiatan', $hari_tanggal_kegiatan);
        $templateProcessor->setValue('waktu_kegiatan', htmlspecialchars($validated['waktu_kegiatan']));
        $templateProcessor->setValue('tempat_kegiatan', htmlspecialchars($validated['tempat_kegiatan']));

        // Multi-line values
        // Convert HTML from TinyMCE to Word line breaks for undangan
        $undanganHtml = $validated['undangan'];
        $undanganHtml = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $undanganHtml);
        $undanganHtml = strip_tags($undanganHtml);
        $undanganHtml = preg_replace("/\n\n+/", "\n\n", trim($undanganHtml));
        $undanganFormatted = str_replace("\n", '</w:t><w:br/><w:t>', htmlspecialchars($undanganHtml));
        $templateProcessor->setValue('undangan', $undanganFormatted);

        // Convert HTML from TinyMCE to Word line breaks for isi_undangan
        $isiUndanganHtml = $validated['isi_undangan'];
        $isiUndanganHtml = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $isiUndanganHtml);
        $isiUndanganHtml = strip_tags($isiUndanganHtml);
        // Clean up excessive newlines
        $isiUndanganHtml = preg_replace("/\n\n+/", "\n\n", trim($isiUndanganHtml));
        $isiUndanganFormatted = str_replace("\n", '</w:t><w:br/><w:t>', htmlspecialchars($isiUndanganHtml));
        $templateProcessor->setValue('isi_undangan', $isiUndanganFormatted);

        $agendaFormatted = str_replace("\n", '</w:t><w:br/><w:t>', htmlspecialchars($validated['agend_kegiatan']));
        $templateProcessor->setValue('agend_kegiatan', $agendaFormatted);

        $fileName = 'Surat_Undangan_' . time() . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function exportDaftarHadir(Request $request)
    {
        $validated = $request->validate([
            'judul_kegiatan' => 'required|string',
            'tanggal_kegiatan' => 'required|date',
            'pimpinan_id' => 'required|exists:pegawais,id',
            'pembuat_id' => 'required|exists:pegawais,id',
            'jumlah_baris' => 'required|integer|min:1|max:100'
        ]);

        $pimpinan = Pegawai::find($validated['pimpinan_id']);
        $pembuat = Pegawai::find($validated['pembuat_id']);
        $jumlah_baris = $validated['jumlah_baris'];

        \Carbon\Carbon::setLocale('id');
        $tanggal = \Carbon\Carbon::parse($validated['tanggal_kegiatan'])->translatedFormat('d F Y');

        return view('template_word.daftar_hadir', compact('validated', 'tanggal', 'pimpinan', 'pembuat', 'jumlah_baris'));
    }
}
