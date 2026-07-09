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
            $table->json('anomaly_indicators')->nullable()->after('heatmap_path');
        });
    }

    public function down(): void
    {
        Schema::table('a_i_results', function (Blueprint $table) {
            $table->dropColumn('anomaly_indicators');
        });
    }
};
