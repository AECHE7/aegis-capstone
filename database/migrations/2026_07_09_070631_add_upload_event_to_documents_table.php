<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('upload_event', 20)->default('initial')->after('document_type'); // initial, replaced, deleted
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete()->after('upload_event');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn(['upload_event', 'uploaded_by']);
        });
    }
};
