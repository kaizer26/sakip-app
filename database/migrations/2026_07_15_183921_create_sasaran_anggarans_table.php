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
        Schema::create('sasaran_anggarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode'); // 3-digit code
            $table->integer('tahun');
            $table->decimal('pagu_awal', 20, 2)->default(0);
            $table->decimal('pagu_revisi', 20, 2)->default(0);
            $table->decimal('realisasi_tw1', 20, 2)->default(0);
            $table->decimal('realisasi_tw2', 20, 2)->default(0);
            $table->decimal('realisasi_tw3', 20, 2)->default(0);
            $table->decimal('realisasi_tw4', 20, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['kode', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sasaran_anggarans');
    }
};
