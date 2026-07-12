<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel media untuk foto/file yang diembed ke dalam konten rich-text
        Schema::create('indikator_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->string('field')->comment('Field asal: basis_data atau dasar_hitung');
            $table->string('file_path')->comment('Path file di storage/public');
            $table->string('original_name')->nullable()->comment('Nama file asli');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('Ukuran file dalam bytes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_media');
    }
};
