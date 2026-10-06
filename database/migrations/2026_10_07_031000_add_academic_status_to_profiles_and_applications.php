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
        if (Schema::hasTable('student_profiles') && !Schema::hasColumn('student_profiles', 'academic_status')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->string('academic_status')->default('Regular')->after('year_level');
            });
        }

        if (Schema::hasTable('applications') && !Schema::hasColumn('applications', 'academic_status')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('academic_status')->default('Regular')->after('gwa');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_profiles') && Schema::hasColumn('student_profiles', 'academic_status')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropColumn('academic_status');
            });
        }

        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'academic_status')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('academic_status');
            });
        }
    }
};
