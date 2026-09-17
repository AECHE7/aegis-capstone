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
        if (!Schema::hasColumn('users', 'dpa_consent_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('dpa_consent_at')->nullable()->after('has_completed_tour');
            });
        }

        if (!Schema::hasColumn('applications', 'dpa_consent_at')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->timestamp('dpa_consent_at')->nullable()->after('previous_application_id');
                $table->boolean('submitted_after_hours')->default(false)->after('dpa_consent_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'dpa_consent_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dpa_consent_at');
            });
        }

        if (Schema::hasColumn('applications', 'dpa_consent_at')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn(['dpa_consent_at', 'submitted_after_hours']);
            });
        }
    }
};
