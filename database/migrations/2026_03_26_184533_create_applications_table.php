<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained()->onDelete('set null');
            $table->string('program_name');
            $table->string('gwa');
            $table->string('status')->default('Pending');
            
            // --- The Audit Trail ---
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('evaluated_by')->nullable();
            $table->foreign('evaluated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};