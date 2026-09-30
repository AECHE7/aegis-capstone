<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserMfaDevice;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        Setting::set('mfa_enforcement', 'all');
    }

    public function test_otp_is_stored_hashed_at_rest_and_hidden_from_serialization(): void
    {
        $user = User::factory()->create([
            'email' => 'student.security@clsu.edu.ph',
            'role' => 'student',
            'password' => Hash::make('Password123!'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'student.security@clsu.edu.ph',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('login.mfa'));

        $user->refresh();
        // In unit tests, OTP is '123456'. Database must store its SHA-256 hash, NOT plaintext.
        $this->assertNotEquals('123456', $user->otp_code);
        $this->assertEquals(hash('sha256', '123456'), $user->otp_code);

        // Verify otp_code is hidden from JSON / array serialization
        $userArray = $user->toArray();
        $this->assertArrayNotHasKey('otp_code', $userArray);

        // Verify submitting plaintext '123456' verifies successfully
        $verifyResponse = $this->withSession(['mfa_user_id' => $user->id])
            ->post(route('login.mfa.verify'), [
                'code' => '123456',
            ]);

        $verifyResponse->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_remembered_device_token_is_hashed_in_database(): void
    {
        $user = User::factory()->create([
            'email' => 'student.device@clsu.edu.ph',
            'role' => 'student',
            'otp_code' => hash('sha256', '123456'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->withSession(['mfa_user_id' => $user->id])
            ->post(route('login.mfa.verify'), [
                'code' => '123456',
                'remember_device' => '1',
            ]);

        $response->assertRedirect(route('student.dashboard'));

        // Check that a device record was created
        $device = UserMfaDevice::where('user_id', $user->id)->first();
        $this->assertNotNull($device);

        // Retrieve the cookie that was queued
        $cookie = $response->getCookie('mfa_device_token');
        $this->assertNotNull($cookie);
        $rawCookieToken = $cookie->getValue();

        // The stored device_token MUST be the SHA-256 hash of the raw cookie, NOT the raw cookie itself
        $this->assertNotEquals($rawCookieToken, $device->device_token);
        $this->assertEquals(hash('sha256', $rawCookieToken), $device->device_token);
    }

    public function test_password_change_forbids_reusing_current_password_and_revokes_trusted_devices(): void
    {
        $user = User::factory()->create([
            'email' => 'student.pwchange@clsu.edu.ph',
            'role' => 'student',
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        // Attach a trusted device
        UserMfaDevice::create([
            'user_id' => $user->id,
            'device_token' => hash('sha256', 'sample_device_token'),
            'expires_at' => now()->addDays(30),
        ]);

        $this->assertCount(1, $user->mfaDevices);

        // 1. Attempting to change to the identical password should fail
        $sameResponse = $this->actingAs($user)->post(route('profile.security.update'), [
            'current_password' => 'CurrentPassword123!',
            'password' => 'CurrentPassword123!',
            'password_confirmation' => 'CurrentPassword123!',
        ]);

        $sameResponse->assertSessionHasErrors(['password']);

        // 2. Updating with a new strong password should succeed and revoke all trusted devices
        $successResponse = $this->actingAs($user)->post(route('profile.security.update'), [
            'current_password' => 'CurrentPassword123!',
            'password' => 'BrandNewPassword456!',
            'password_confirmation' => 'BrandNewPassword456!',
        ]);

        $successResponse->assertSessionHas('success');
        $user->refresh();

        $this->assertTrue(Hash::check('BrandNewPassword456!', $user->password));
        $this->assertCount(0, $user->mfaDevices);
    }

    public function test_sensitive_routes_enforce_rate_limiting(): void
    {
        // 1. Reset password route throttling
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('password.store'), [
                'token' => 'invalid_token',
                'email' => 'target@clsu.edu.ph',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);
            $response->assertStatus(302);
        }

        // 6th attempt must be blocked by throttle:5,1
        $blockedResponse = $this->post(route('password.store'), [
            'token' => 'invalid_token',
            'email' => 'target@clsu.edu.ph',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $blockedResponse->assertStatus(429);
    }

    public function test_security_audit_artisan_command_passes(): void
    {
        $this->artisan('aegis:security-audit', ['--json' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('checks');
    }
}
