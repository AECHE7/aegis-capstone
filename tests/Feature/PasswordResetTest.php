<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a verified student user
        $this->student = User::create([
            'name' => 'Browser Student',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('oldpassword123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
        $response->assertSee('Email Address');
    }

    public function test_student_can_request_password_reset_link_with_valid_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'studenttest@clsu.edu.ph',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        // Assert reset password notification was compiled and logged in email_logs
        $this->assertDatabaseHas('email_logs', [
            'recipient' => 'studenttest@clsu.edu.ph',
            'subject' => '[A.E.G.I.S.] Reset Your Password',
        ]);
    }

    public function test_student_cannot_request_password_reset_link_with_invalid_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@clsu.edu.ph',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_password_screen_can_be_rendered_with_valid_token(): void
    {
        $token = Password::createToken($this->student);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => 'studenttest@clsu.edu.ph',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('studenttest@clsu.edu.ph');
    }

    public function test_student_can_reset_password_with_valid_token_and_matching_passwords(): void
    {
        $token = Password::createToken($this->student);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'studenttest@clsu.edu.ph',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Assert user password is changed
        $this->student->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->student->password));
    }
}
