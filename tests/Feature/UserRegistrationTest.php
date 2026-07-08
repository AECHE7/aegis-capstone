<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomVerifyEmailNotification;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('CLSU Student Email');
    }

    public function test_student_can_register_with_clsu_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'studenttest@clsu2.edu.ph',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'name' => 'Test Student',
            'email' => 'studenttest@clsu2.edu.ph',
            'role' => 'student',
            'email_verified_at' => null
        ]);

        $user = User::where('email', 'studenttest@clsu2.edu.ph')->first();
        Notification::assertSentTo($user, CustomVerifyEmailNotification::class);
    }

    public function test_student_cannot_register_with_non_clsu_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'studenttest@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'studenttest@gmail.com'
        ]);
    }

    public function test_unverified_student_cannot_access_student_routes(): void
    {
        // Create an unverified user
        $user = User::create([
            'name' => 'Unverified Student',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => null
        ]);

        // Attempt accessing apply page
        $response = $this->actingAs($user)->get('/apply');

        // Should redirect to verification notice prompt
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_student_can_access_student_routes(): void
    {
        // Create a verified user
        $user = User::create([
            'name' => 'Verified Student',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);

        // Create a scholarship to prevent create view crashes
        Scholarship::create([
            'name' => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($user)->get('/apply');
        $response->assertStatus(200);
    }

    public function test_unverified_student_login_redirects_to_verification_notice(): void
    {
        $user = User::create([
            'name' => 'Unverified Student',
            'email' => 'unverified@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => null,
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post('/login', [
            'email' => 'unverified@clsu.edu.ph',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('login.mfa'));

        $mfaResponse = $this->withSession(['mfa_user_id' => $user->id])
            ->post('/login/mfa', [
                'code' => '123456',
            ]);

        $mfaResponse->assertRedirect(route('verification.notice'));
    }

    public function test_verified_student_login_redirects_to_dashboard(): void
    {
        $user = User::create([
            'name' => 'Verified Student',
            'email' => 'verified@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post('/login', [
            'email' => 'verified@clsu.edu.ph',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('login.mfa'));

        $mfaResponse = $this->withSession(['mfa_user_id' => $user->id])
            ->post('/login/mfa', [
                'code' => '123456',
            ]);

        $mfaResponse->assertRedirect(route('student.dashboard'));
    }
}
