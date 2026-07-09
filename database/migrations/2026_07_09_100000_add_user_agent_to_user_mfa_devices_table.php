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
        if (Schema::hasTable('user_mfa_devices')) {
            Schema::table('user_mfa_devices', function (Blueprint $table) {
                if (!Schema::hasColumn('user_mfa_devices', 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_mfa_devices')) {
            Schema::table('user_mfa_devices', function (Blueprint $table) {
                if (Schema::hasColumn('user_mfa_devices', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
            });
        }
    }
};
