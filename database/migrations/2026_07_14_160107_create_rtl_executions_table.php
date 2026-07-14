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
        Schema::create('rtl_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rtl_id')->constrained('rtls')->onDelete('cascade');
            $table->integer('triwulan');
            $table->integer('tahun');
            $table->text('catatan_progres');
            $table->string('file_bukti_dukung')->nullable();
            $table->string('verified_by')->nullable(); // NIP of the verifier
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtl_executions');
    }
};
