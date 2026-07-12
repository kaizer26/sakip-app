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
        Schema::create('tabel_ros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('triwulan');
            $table->string('ro');
            $table->decimal('realisasi_volume_ro', 15, 2)->default(0);
            $table->decimal('progres_ro', 5, 2)->default(0);
            $table->decimal('pagu_awal', 20, 2)->default(0);
            $table->decimal('pagu_revisi', 20, 2)->default(0);
            $table->decimal('pagu_sisa', 20, 2)->default(0);
            $table->decimal('pagu_realisasi', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_ros');
    }
};
