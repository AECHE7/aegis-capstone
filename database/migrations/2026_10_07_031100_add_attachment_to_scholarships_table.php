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
        if (Schema::hasTable('scholarships') && !Schema::hasColumn('scholarships', 'attachment_path')) {
            Schema::table('scholarships', function (Blueprint $table) {
                $table->string('attachment_path')->nullable()->after('quota');
                $table->string('attachment_name')->nullable()->after('attachment_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('scholarships') && Schema::hasColumn('scholarships', 'attachment_path')) {
            Schema::table('scholarships', function (Blueprint $table) {
                $table->dropColumn(['attachment_path', 'attachment_name']);
            });
        }
    }
};
