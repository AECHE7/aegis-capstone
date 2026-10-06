<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DummyAccountBypassTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('mfa_enforcement', 'all');
    }

    public function test_dummy_staff_admin_bypasses_mfa_and_auto_verifies_email(): void
    {
        $admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => null, // Test auto-verification
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'admin@clsu.edu.ph',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $admin->refresh();
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_dummy_director_bypasses_mfa_and_auto_verifies_email(): void
    {
        $director = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => null,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'director@clsu.edu.ph',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('superadmin.scholarships'));
        $this->assertAuthenticatedAs($director);

        $director->refresh();
        $this->assertNotNull($director->email_verified_at);
    }

    public function test_dummy_superadmin_alias_bypasses_mfa(): void
    {
        $superadmin = User::create([
            'name' => 'CLSU Super Admin',
            'email' => 'superadmin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $responseSuper = $this->post(route('login.submit'), [
            'email' => 'superadmin@clsu.edu.ph',
            'password' => 'password',
        ]);
        $responseSuper->assertRedirect(route('superadmin.scholarships'));
        $this->assertAuthenticatedAs($superadmin);
    }


    public function test_universal_demo_otp_verifies_dummy_account_on_mfa_page(): void
    {
        $admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'otp_code' => hash('sha256', '999999'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Submit universal demo OTP '123456'
        $response = $this->withSession(['mfa_user_id' => $admin->id])
            ->post(route('login.mfa.verify'), [
                'code' => '123456',
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_universal_demo_otp_000000_verifies_dummy_account(): void
    {
        $director = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'otp_code' => hash('sha256', '888888'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Submit universal demo OTP '000000'
        $response = $this->withSession(['mfa_user_id' => $director->id])
            ->post(route('login.mfa.verify'), [
                'code' => '000000',
            ]);

        $response->assertRedirect(route('superadmin.scholarships'));
        $this->assertAuthenticatedAs($director);
    }

    public function test_dummy_account_on_verify_email_prompt_auto_redirects(): void
    {
        $admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('verification.notice'));
        $response->assertRedirect(route('admin.dashboard'));

        $admin->refresh();
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_verification_status_endpoint_auto_verifies_dummy_account(): void
    {
        $director = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($director)->get(route('verification.status'));
        $response->assertOk();
        $response->assertJson([
            'verified' => true,
            'redirect_url' => route('superadmin.scholarships'),
        ]);

        $director->refresh();
        $this->assertNotNull($director->email_verified_at);
    }

    public function test_non_dummy_account_still_strictly_requires_mfa(): void
    {
        $user = User::create([
            'name' => 'Regular Staff Member',
            'email' => 'regular.staff@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'regular.staff@clsu.edu.ph',
            'password' => 'password123',
        ]);

        // Regular staff must strictly be sent to MFA
        $response->assertRedirect(route('login.mfa'));
        $this->assertFalse(auth()->check());
    }

    public function test_dummy_student_can_login_with_standard_password(): void
    {
        $student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'student@clsu.edu.ph',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_student_demo_alias_auto_provisions_and_accepts_evaluation_password(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'student.demo@clsu.edu.ph',
            'password' => 'StudentDemo2026!',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertTrue(auth()->check());
        $this->assertEquals('student.demo@clsu.edu.ph', auth()->user()->email);
        $this->assertEquals('student', auth()->user()->role);
    }
}
