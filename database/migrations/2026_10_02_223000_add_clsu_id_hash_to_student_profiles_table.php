<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\StudentProfile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('clsu_id_hash', 64)->nullable()->index()->after('clsu_id_number');
        });

        // Backfill hashes for existing profiles
        foreach (StudentProfile::cursor() as $profile) {
            if (!empty($profile->clsu_id_number)) {
                $normalized = strtoupper(preg_replace('/\s+/', '', (string) $profile->clsu_id_number));
                DB::table('student_profiles')
                    ->where('id', $profile->id)
                    ->update(['clsu_id_hash' => hash('sha256', $normalized)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn('clsu_id_hash');
        });
    }
};
