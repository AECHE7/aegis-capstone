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
            if (!Schema::hasColumn('a_i_results', 'deep_analysis_report')) {
                $table->json('deep_analysis_report')->nullable()->after('cropped_patch_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('a_i_results', function (Blueprint $table) {
            if (Schema::hasColumn('a_i_results', 'deep_analysis_report')) {
                $table->dropColumn('deep_analysis_report');
            }
        });
    }
};
