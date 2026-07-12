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
        Schema::table('targets', function (Blueprint $table) {
            $table->decimal('target_x_tw1', 15, 2)->nullable();
            $table->decimal('target_x_tw2', 15, 2)->nullable();
            $table->decimal('target_x_tw3', 15, 2)->nullable();
            $table->decimal('target_x_tw4', 15, 2)->nullable();

            $table->decimal('target_y_tw1', 15, 2)->nullable();
            $table->decimal('target_y_tw2', 15, 2)->nullable();
            $table->decimal('target_y_tw3', 15, 2)->nullable();
            $table->decimal('target_y_tw4', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropColumn([
                'target_x_tw1', 'target_x_tw2', 'target_x_tw3', 'target_x_tw4',
                'target_y_tw1', 'target_y_tw2', 'target_y_tw3', 'target_y_tw4'
            ]);
        });
    }
};
