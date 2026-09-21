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
        Schema::table('uat_feedbacks', function (Blueprint $table) {
            if (!Schema::hasColumn('uat_feedbacks', 'performance_efficiency')) {
                $table->integer('performance_efficiency')->default(5)->after('functional_suitability');
            }
            if (!Schema::hasColumn('uat_feedbacks', 'compatibility')) {
                $table->integer('compatibility')->default(5)->after('security');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uat_feedbacks', function (Blueprint $table) {
            if (Schema::hasColumn('uat_feedbacks', 'performance_efficiency')) {
                $table->dropColumn('performance_efficiency');
            }
            if (Schema::hasColumn('uat_feedbacks', 'compatibility')) {
                $table->dropColumn('compatibility');
            }
        });
    }
};
