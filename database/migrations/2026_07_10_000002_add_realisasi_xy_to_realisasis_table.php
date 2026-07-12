<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            // Nilai X per triwulan: jumlah realisasi yang memenuhi syarat (pembilang)
            $table->decimal('realisasi_x', 15, 4)->nullable()->after('realisasi_kumulatif')
                  ->comment('Nilai X: jumlah realisasi yang memenuhi target (pembilang)');

            // Nilai Y per triwulan: jumlah total realisasi (penyebut)
            $table->decimal('realisasi_y', 15, 4)->nullable()->after('realisasi_x')
                  ->comment('Nilai Y: jumlah total realisasi keseluruhan (penyebut)');
        });
    }

    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn(['realisasi_x', 'realisasi_y']);
        });
    }
};
