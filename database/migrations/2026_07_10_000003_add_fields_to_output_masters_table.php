<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('output_masters', function (Blueprint $table) {
            // Penjelasan/deskripsi rinci Rincian Output (RO)
            $table->text('penjelasan_ro')->nullable()->after('nama_output')
                  ->comment('Penjelasan deskriptif Rincian Output');

            // Target volume RO (untuk dibandingkan dengan realisasi volume)
            $table->decimal('target_volume', 15, 4)->nullable()->after('penjelasan_ro')
                  ->comment('Target volume/jumlah output yang direncanakan');
        });
    }

    public function down(): void
    {
        Schema::table('output_masters', function (Blueprint $table) {
            $table->dropColumn(['penjelasan_ro', 'target_volume']);
        });
    }
};
