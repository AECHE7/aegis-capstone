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
            if (!Schema::hasColumn('a_i_results', 'detected_software')) {
                $table->string('detected_software')->nullable()->after('classification');
            }
            if (!Schema::hasColumn('a_i_results', 'cropped_patch_data')) {
                $table->longText('cropped_patch_data')->nullable()->after('heatmap_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('a_i_results', function (Blueprint $table) {
            if (Schema::hasColumn('a_i_results', 'detected_software')) {
                $table->dropColumn('detected_software');
            }
            if (Schema::hasColumn('a_i_results', 'cropped_patch_data')) {
                $table->dropColumn('cropped_patch_data');
            }
        });
    }
};
