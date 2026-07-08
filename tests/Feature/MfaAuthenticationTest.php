<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MfaAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_mfa_verify_page()
    {
        $user = User::factory()->create([
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'student@clsu.edu.ph',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login.mfa'));
        $this->assertEquals($user->id, session('mfa_user_id'));
        $this->assertFalse(auth()->check()); // Not authenticated yet
    }

    public function test_submitting_correct_mfa_otp_authenticates_user()
    {
        $user = User::factory()->create([
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Simulating the temporary session state
        $response = $this->withSession(['mfa_user_id' => $user->id])
            ->post('/login/mfa', [
                'code' => '123456',
            ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_submitting_incorrect_mfa_otp_fails()
    {
        $user = User::factory()->create([
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['mfa_user_id' => $user->id])
            ->post('/login/mfa', [
                'code' => '999999',
            ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertFalse(auth()->check());
    }
}
