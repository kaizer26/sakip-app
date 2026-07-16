<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->text('target_realisasi')->nullable()->after('penjelasan_lainnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->dropColumn('target_realisasi');
        });
    }
};
