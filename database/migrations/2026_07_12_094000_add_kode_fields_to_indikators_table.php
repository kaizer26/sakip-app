<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->string('kode_tujuan')->nullable()->after('kode')
                  ->comment('Kode Tujuan (misal: T1)');
            $table->string('kode_sasaran')->nullable()->after('kode_tujuan')
                  ->comment('Kode Sasaran (misal: 1.1.1)');
            $table->string('kode_indikator_kinerja')->nullable()->after('kode_sasaran')
                  ->comment('Kode Indikator Kinerja (misal: 1.1.1.1)');
        });
    }

    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn(['kode_tujuan', 'kode_sasaran', 'kode_indikator_kinerja']);
        });
    }
};
