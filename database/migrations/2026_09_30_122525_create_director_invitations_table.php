<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('director_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('invited_by_email');           // Master account email who sent it
            $table->string('recipient_email');            // Client email to become Director
            $table->string('recipient_name')->nullable(); // Optional display name
            $table->string('token', 80)->unique();        // Secure random token
            $table->timestamp('expires_at');              // Link validity (48 hrs)
            $table->timestamp('accepted_at')->nullable(); // Null = pending
            $table->unsignedBigInteger('accepted_by_user_id')->nullable(); // FK to users.id after accept
            $table->timestamps();

            $table->index(['token']);
            $table->index(['recipient_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_invitations');
    }
};
