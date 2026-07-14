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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->integer('triwulan');
            $table->integer('tahun');
            $table->enum('status_kendala', ['Selesai', 'Sebagian Selesai', 'Belum Ditangani']);
            $table->text('deskripsi');
            $table->text('solusi_sementara')->nullable();
            $table->string('pegawai_nip'); // User who reported
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
