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
        Schema::table('a_i_results', function (Blueprint $table) {
            if (!Schema::hasColumn('a_i_results', 'heatmap_data')) {
                $table->longText('heatmap_data')->nullable()->after('heatmap_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('a_i_results', function (Blueprint $table) {
            if (Schema::hasColumn('a_i_results', 'heatmap_data')) {
                $table->dropColumn('heatmap_data');
            }
        });
    }
};
