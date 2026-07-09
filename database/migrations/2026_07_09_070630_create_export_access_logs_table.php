<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('export_type'); // audit_log, email_log, ai_scan_log, auth_log, admin_action_log, evaluation_log, config_change_log, scholarship_change_log, student_timeline_log, doc_upload_log
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('format', 10); // csv, pdf
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_access_logs');
    }
};
