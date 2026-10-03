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
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['status', 'is_archived'], 'idx_apps_status_archived');
            $table->index(['assigned_to', 'status'], 'idx_apps_assigned_status');
            $table->index(['user_id', 'academic_term_id'], 'idx_apps_user_term');
            $table->index('created_at', 'idx_apps_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_apps_status_archived');
            $table->dropIndex('idx_apps_assigned_status');
            $table->dropIndex('idx_apps_user_term');
            $table->dropIndex('idx_apps_created_at');
        });
    }
};
