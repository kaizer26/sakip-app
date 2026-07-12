# Penambahan Target Triwulanan untuk Variabel X dan Y

Penyesuaian ini bertujuan untuk menambahkan kapabilitas pencatatan **Target X** dan **Target Y** per triwulan (jika indikator tersebut memiliki komponen X dan Y), serta menampilkannya di halaman pengisian realisasi.

## User Review Required

> [!IMPORTANT]
> Silakan periksa rencana di bawah ini dan klik **Proceed** jika sudah sesuai dengan kebutuhan Anda.

---

## Proposed Changes

### 1. Database & Model
Kita akan menambahkan 8 kolom baru ke tabel `targets` untuk mengakomodir target X dan Y di masing-masing triwulan.

#### [NEW] `database/migrations/2026_07_10_000006_add_target_xy_to_targets_table.php`
Menambahkan kolom berikut ke tabel `targets`:
- `target_x_tw1` sampai `target_x_tw4` (tipe: decimal, nullable)
- `target_y_tw1` sampai `target_y_tw4` (tipe: decimal, nullable)

#### [MODIFY] `app/Models/Target.php`
- Menambahkan 8 kolom di atas ke dalam property `$fillable` agar bisa disimpan massal.

### 2. Form Target di Master Indikator
#### [MODIFY] `resources/views/indikator/index.blade.php`
- Di **Modal Kelola Indikator > Tab Target TW**:
  - Secara default, form hanya menampilkan "Target TW 1-4" (target capaian utama).
  - Jika indikator yang dipilih memiliki nilai pada **Deskripsi X** atau **Deskripsi Y** (artinya indikator ini menggunakan formula X/Y), maka form akan *expand* untuk menampilkan kolom input **Target X** (TW 1-4) dan **Target Y** (TW 1-4).
  - Jika tidak ada X dan Y, form tersebut akan tetap sederhana seperti sebelumnya (menyembunyikan input target X dan Y).

### 3. Controller Logic
#### [MODIFY] `app/Http/Controllers/TargetController.php`
- **`update` method**: Menambahkan proses penerimaan parameter target X dan Y, melakukan sanitasi koma ke titik, lalu menyimpannya ke database.
- **`show` method**: Secara otomatis mengembalikan data target X dan Y untuk dimuat oleh JavaScript.

#### [MODIFY] `app/Http/Controllers/RealisasiController.php`
- **`getContext` method**: Mengambil nilai `target_x_tw[N]` dan `target_y_tw[N]` sesuai triwulan yang dipilih user, lalu memberikannya ke response JSON halaman input realisasi.

### 4. Form Input Realisasi
#### [MODIFY] `resources/views/realisasi/entry.blade.php`
- Di bagian form **Nilai X / Y (Dasar Hitung)**, sistem akan menampilkan target per triwulan tepat di atas atau di sebelah field input X dan Y sebagai panduan (contoh: "Target X TW I: 15").
- Target capaian utama tetap muncul seperti biasa.

---

## Verification Plan

### Automated Tests
- Menjalankan `php artisan migrate` untuk memastikan tabel `targets` ter-update tanpa masalah.
- Validasi PHP syntax dengan `php -l` di semua file yang diubah.

### Manual Verification
1. Login sebagai Admin.
2. Buka Master Indikator, pilih indikator yang ada X dan Y, buka form Kelola -> Tab Target. Pastikan input Target X dan Y muncul.
3. Masukkan angka target, lalu simpan.
4. Buka indikator lain yang *tidak* memiliki X dan Y. Pastikan input target X dan Y *tidak* muncul.
5. Masuk ke halaman **Input Realisasi** indikator dengan X dan Y. Pastikan informasi Target X dan Target Y untuk triwulan yang dipilih tampil dengan benar di layar.
