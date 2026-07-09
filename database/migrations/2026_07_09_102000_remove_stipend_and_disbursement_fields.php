<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['bank_name', 'bank_account_name', 'bank_account_number', 'disbursement_method', 'disbursement_account_name', 'disbursement_account_number'] as $col) {
                if (Schema::hasColumn('student_profiles', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'stipend_amount')) {
                $table->dropColumn('stipend_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->unsignedInteger('stipend_amount')->default(0);
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
        });
    }
};
