<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            // Field X: label/deskripsi pembilang (contoh: "Jumlah Publikasi yang berkualitas")
            $table->string('definisi_x')->nullable()->after('penjelasan_lainnya')
                  ->comment('Deskripsi variabel X (pembilang) formula capaian indikator');

            // Field Y: label/deskripsi penyebut (contoh: "Jumlah seluruh Publikasi")
            $table->string('definisi_y')->nullable()->after('definisi_x')
                  ->comment('Deskripsi variabel Y (penyebut) formula capaian indikator');

            // Basis data: konten rich-text (HTML) termasuk foto & rumus LaTeX
            $table->longText('basis_data')->nullable()->after('definisi_y')
                  ->comment('Basis data indikator: konten rich-text, foto, dan rumus');

            // Ubah dasar_hitung dari TEXT ke LONGTEXT untuk mendukung konten besar
            $table->longText('dasar_hitung')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn(['definisi_x', 'definisi_y', 'basis_data']);
            $table->text('dasar_hitung')->nullable()->change();
        });
    }
};
