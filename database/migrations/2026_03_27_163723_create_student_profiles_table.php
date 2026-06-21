<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            // Link directly to the users table (One-to-One)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // University Data
            $table->string('clsu_id_number')->unique();
            $table->string('college');
            $table->string('course');
            $table->string('year_level');
            $table->string('contact_number')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};