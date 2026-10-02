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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->text('province')->nullable()->after('year_level');
            $table->text('city_municipality')->nullable()->after('province');
            $table->text('barangay')->nullable()->after('city_municipality');
            $table->text('street_address')->nullable()->after('barangay');
            $table->text('address')->nullable()->after('street_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['province', 'city_municipality', 'barangay', 'street_address', 'address']);
        });
    }
};
