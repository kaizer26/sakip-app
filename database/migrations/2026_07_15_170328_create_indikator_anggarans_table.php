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
        Schema::create('indikator_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->integer('tahun');
            $table->decimal('pagu_awal', 20, 2)->default(0);
            $table->decimal('pagu_revisi', 20, 2)->default(0);
            $table->decimal('realisasi_tw1', 20, 2)->default(0);
            $table->decimal('realisasi_tw2', 20, 2)->default(0);
            $table->decimal('realisasi_tw3', 20, 2)->default(0);
            $table->decimal('realisasi_tw4', 20, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['indikator_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_anggarans');
    }
};
