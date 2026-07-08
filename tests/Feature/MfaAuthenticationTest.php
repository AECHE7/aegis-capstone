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

    public function test_submitting_correct_mfa_otp_sets_remember_device_cookie_when_checked()
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
                'code' => '123456',
                'remember_device' => 'on',
            ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertCookie('mfa_device_token');
        
        $this->assertDatabaseHas('user_mfa_devices', [
            'user_id' => $user->id,
        ]);
    }

    public function test_login_bypasses_mfa_with_valid_remember_device_cookie()
    {
        $user = User::factory()->create([
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $token = \Illuminate\Support\Str::random(60);
        $userAgentHash = hash('sha256', 'Symfony'); // Default test user agent is Symfony

        \App\Models\UserMfaDevice::create([
            'user_id' => $user->id,
            'device_token' => $token,
            'ip_address' => '127.0.0.1',
            'user_agent_hash' => $userAgentHash,
            'expires_at' => now()->addDays(30),
        ]);

        // Making standard login request with the encrypted cookie
        $response = $this->withCookie('mfa_device_token', $token)
            ->post('/login', [
                'email' => 'student@clsu.edu.ph',
                'password' => 'password123',
            ]);

        // Should bypass route('login.mfa') and go straight to student.dashboard
        $response->assertRedirect(route('student.dashboard'));
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_login_does_not_bypass_mfa_with_invalid_user_agent_cookie()
    {
        $user = User::factory()->create([
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $token = \Illuminate\Support\Str::random(60);
        
        // Storing with a different user agent hash
        \App\Models\UserMfaDevice::create([
            'user_id' => $user->id,
            'device_token' => $token,
            'ip_address' => '127.0.0.1',
            'user_agent_hash' => 'different_hash',
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->withCookie('mfa_device_token', $token)
            ->post('/login', [
                'email' => 'student@clsu.edu.ph',
                'password' => 'password123',
            ]);

        // Should NOT bypass and redirect to MFA
        $response->assertRedirect(route('login.mfa'));
        $this->assertFalse(auth()->check());
    }
}
