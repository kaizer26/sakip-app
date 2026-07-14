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
        Schema::create('rtls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->text('deskripsi_rtl');
            $table->string('pic_nip'); // Assuming user approved pegawai_nip based on "gunakan pegawai_nip"
            $table->date('due_date');
            $table->enum('status_rtl', ['Open', 'In Progress', 'Selesai', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtls');
    }
};
