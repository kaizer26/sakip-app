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
        Schema::table('indikator_anggarans', function (Blueprint $table) {
            $table->string('kode_kegiatan')->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->string('kode_ro')->nullable();
            $table->string('nama_ro')->nullable();
        });

        Schema::table('sasaran_anggarans', function (Blueprint $table) {
            $table->string('kode_kegiatan')->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->string('kode_ro')->nullable();
            $table->string('nama_ro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_anggarans', function (Blueprint $table) {
            $table->dropColumn(['kode_kegiatan', 'nama_kegiatan', 'kode_ro', 'nama_ro']);
        });

        Schema::table('sasaran_anggarans', function (Blueprint $table) {
            $table->dropColumn(['kode_kegiatan', 'nama_kegiatan', 'kode_ro', 'nama_ro']);
        });
    }
};
