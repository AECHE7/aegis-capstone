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
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code')->nullable()->after('password');
            $table->dateTime('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('has_completed_tour')->default(false)->after('role');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dateTime('scheduled_publish_at')->nullable()->after('content');
            $table->dateTime('scheduled_delete_at')->nullable()->after('scheduled_publish_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('is_renewal')->default(false)->after('status');
            $table->foreignId('previous_application_id')->nullable()->after('is_renewal')->constrained('applications')->nullOnDelete();
            $table->text('forfeit_reason')->nullable()->after('previous_application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['previous_application_id']);
            $table->dropColumn(['is_renewal', 'previous_application_id', 'forfeit_reason']);
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['scheduled_publish_at', 'scheduled_delete_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'has_completed_tour']);
        });
    }
};
