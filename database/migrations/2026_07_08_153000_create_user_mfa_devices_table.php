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
        Schema::create('user_mfa_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_token')->unique();
            $table->string('ip_address')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['user_id', 'device_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mfa_devices');
    }
};
