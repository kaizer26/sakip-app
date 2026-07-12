<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('triwulan')->comment('1-4');
            $table->string('link_bukti_kinerja')->nullable();
            $table->string('link_bukti_tindak_lanjut')->nullable();
            $table->text('penjelasan_lainnya')->nullable();
            $table->timestamps();

            $table->unique(['indikator_id', 'tahun', 'triwulan'], 'capaian_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_kinerjas');
    }
};
