<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - {{ $validated['judul_kegiatan'] }}</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        /* Container Dokumen (Disimulasikan dengan proporsi A4 di layar) */
        .document-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 800px;
            padding: 40px 50px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat/Judul */
        .header {
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            color: #111;
        }

        .header p {
            font-size: 14px;
            margin: 0;
            color: #333;
        }

        .date-line {
            font-weight: bold;
            margin-top: 6px !important;
        }

        /* Desain Tabel */
        table {
            width: 100%;
            border-collapse: separate; /* Menghindari bug Chrome saat print PDF dimana background menimpa border */
            border-spacing: 0;
            border-top: 1.5px solid #000;
            border-left: 1.5px solid #000;
            font-size: 13px;
            color: #000;
        }

        /* Mengatur agar tabel dapat membelah halaman dengan rapi saat dicetak */
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border-bottom: 1.5px solid #000 !important;
            border-right: 1.5px solid #000 !important;
            border-top: none !important;
            border-left: none !important;
            padding: 8px 10px;
            vertical-align: middle;
        }

        /* Header Tabel (Nama Kolom) */
        th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: none;
            height: 25px;
        }

        /* Sub-Header (Nomor Kolom) */
        .sub-header th {
            font-size: 11px;
            font-weight: normal;
            color: #444;
            height: 18px;
            padding: 2px;
        }

        /* Data Baris */
        .data-row {
            height: 55px; /* Tinggi baris untuk area tanda tangan */
        }

        .col-no {
            width: 6%;
            text-align: center;
            font-weight: bold;
        }

        .col-nama {
            width: 34%;
            text-align: left;
            padding-left: 12px;
        }

        .col-jabatan {
            width: 30%;
            text-align: left;
            padding-left: 12px;
        }

        .col-signature {
            width: 15%;
            font-size: 11px;
            vertical-align: top;
            padding-top: 6px;
            padding-left: 8px;
            position: relative;
        }

        /* Kelas Warna Zig-Zag Abu-abu Kebiruan */
        .bg-pattern {
            background-color: #e9edf4 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Layout Tanda Tangan Bawah */
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            font-size: 13px;
            line-height: 1.5;
            page-break-inside: avoid; /* Menghindari pecah halaman di tengah tanda tangan */
        }

        .signature-column {
            width: 48%;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center to push it slightly to the right */
        }

        /* Kelas khusus untuk menggeser tanda tangan kanan ke pojok kanan */
        .signature-column.right {
            align-items: flex-end;
        }

        /* Pembungkus agar konten tanda tangan kanan tetap rata kiri secara internal */
        .signature-wrapper {
            width: 250px;
            display: flex;
            flex-direction: column;
        }

        .signature-space {
            height: 75px; /* Tinggi space kosong untuk tanda tangan basah & stempel */
            position: relative;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .signature-nip {
            margin: 0;
        }

        /* Tombol Cetak / Salin (Hanya muncul di layar browser, tersembunyi saat diprint) */
        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
            max-width: 800px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            background-color: #1d4ed8;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            background-color: #4b5563;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }

        /* Notifikasi Toast Elegan (Menggantikan alert bawaan) */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
            z-index: 9999;
            pointer-events: none;
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        /* Pengaturan Cetak Khusus (A4 & Repeat Header) */
        @media print {
            /* Menentukan ukuran kertas fisik menjadi A4 Portrait */
            @page {
                size: A4 portrait;
                margin: 20mm 15mm 20mm 15mm; /* Margin atas, kanan, bawah, kiri */
            }

            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table {
                border-top: 1.5px solid #000 !important;
                border-left: 1.5px solid #000 !important;
            }

            th, td {
                border-bottom: 1.5px solid #000 !important;
                border-right: 1.5px solid #000 !important;
                border-top: none !important;
                border-left: none !important;
            }

            .document-container {
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }

            .action-bar {
                display: none;
            }

            /* Memaksa elemen thead agar selalu diulang (repeat header) di setiap halaman baru */
            thead {
                display: table-header-group;
            }

            /* Mencegah pemotongan baris di tengah-tengah teks tanda tangan */
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div style="display: flex; flex-direction: column; width: 100%; align-items: center;">
        
        <!-- Tombol Bantuan Interaktif -->
        <div class="action-bar">
            <button class="btn btn-secondary" onclick="salinTabel()">Salin Tabel untuk Word</button>
            <button class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>

        <!-- Wadah Utama Dokumen -->
        <div class="document-container" id="printable-area">
            
            <!-- Bagian Judul Dokumen -->
            <div class="header">
                <h1>Daftar Hadir Peserta</h1>
                <h1>{{ $validated['judul_kegiatan'] }}</h1>
                <h1>BPS Kabupaten Tapin</h1>
                <p class="date-line">Tanggal {{ $tanggal }}</p>
            </div>

            <!-- Tabel Daftar Hadir -->
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-nama">Nama</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th colspan="2" style="width: 30%;">Tanda Tangan</th>
                    </tr>
                    <tr class="sub-header">
                        <th class="col-no">(1)</th>
                        <th>(2)</th>
                        <th>(3)</th>
                        <th colspan="2">(4)</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $jumlah_baris; $i++)
                    <tr class="data-row">
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td class="col-nama"></td>
                        <td class="col-jabatan"></td>
                        
                        @if($i % 2 == 0)
                            <td class="col-signature">{{ $i + 1 }}.</td>
                            <td class="col-signature bg-pattern"></td>
                        @else
                            <td class="col-signature bg-pattern"></td>
                            <td class="col-signature">{{ $i + 1 }}.</td>
                        @endif
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Area Tanda Tangan Bawah -->
            <div class="signature-container">
                <!-- Sebelah Kiri (Mengetahui Kepala) -->
                <div class="signature-column">
                    <div class="signature-wrapper">
                        <div>Mengetahui:</div>
                        <div style="font-weight: bold;">Kepala BPS Kabupaten Tapin,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ $pimpinan->nama }}</div>
                        <div class="signature-nip">NIP. {{ $pimpinan->nip }}</div>
                    </div>
                </div>

                <!-- Sebelah Kanan (Pembuat Daftar) -->
                <div class="signature-column right">
                    <div class="signature-wrapper">
                        <!-- Spasi kosong di atas agar sejajar dengan baris "Kepala BPS..." di kiri -->
                        <div style="visibility: hidden;">Placeholder</div> 
                        <div style="font-weight: bold;">Pembuat Daftar,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ $pembuat->nama }}</div>
                        <div class="signature-nip">NIP. {{ $pembuat->nip }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Elemen Toast Notifikasi Kustom -->
    <div id="toast" class="toast">Dokumen berhasil disalin!</div>

    <script>
        // Fungsi menampilkan notifikasi toast pengganti alert()
        function tampilkanToast(pesan) {
            const toast = document.getElementById('toast');
            toast.textContent = pesan;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Fungsi pembantu untuk mempermudah penyalinan langsung ke Microsoft Word
        function salinTabel() {
            const range = document.createRange();
            range.selectNode(document.getElementById('printable-area'));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand('copy');
                tampilkanToast('Dokumen berhasil disalin! Sekarang buka Word Anda dan tekan Ctrl + V.');
            } catch (err) {
                tampilkanToast('Maaf, penyalinan otomatis gagal. Silakan blok tabel secara manual lalu salin.');
            }
            window.getSelection().removeAllRanges();
        }
    </script>
</body>
</html>
