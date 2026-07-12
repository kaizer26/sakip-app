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
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn([
                'link_bukti_kinerja',
                'link_bukti_tindak_lanjut',
                'penjelasan_lainnya'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->string('link_bukti_kinerja')->nullable();
            $table->string('link_bukti_tindak_lanjut')->nullable();
            $table->text('penjelasan_lainnya')->nullable();
        });
    }
};
