<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            // Penjelasan/narasi detail kegiatan (untuk Bukti Dukung)
            $table->text('penjelasan_kegiatan')->nullable()->after('uraian')
                  ->comment('Penjelasan/narasi detail kegiatan bukti dukung');

            // Realisasi kegiatan: narasi capaian yang sudah dilakukan
            $table->text('realisasi_kegiatan')->nullable()->after('penjelasan_kegiatan')
                  ->comment('Realisasi/hasil yang dicapai dari kegiatan ini');
        });
    }

    public function down(): void
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn(['penjelasan_kegiatan', 'realisasi_kegiatan']);
        });
    }
};
