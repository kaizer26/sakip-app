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
            $table->decimal('target_tahunan_x', 15, 2)->nullable()->after('target_tahunan');
            $table->decimal('target_tahunan_y', 15, 2)->nullable()->after('target_tahunan_x');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn(['target_tahunan_x', 'target_tahunan_y']);
        });
    }
};
