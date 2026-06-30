<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthSecurityEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private User $activeStudent;
    private User $deactivatedStudent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create active, verified student
        $this->activeStudent = User::create([
            'name' => 'Active Student',
            'email' => 'active@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create deactivated student
        $this->deactivatedStudent = User::create([
            'name' => 'Deactivated Student',
            'email' => 'deactivated@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
            'is_active' => false,
        ]);
    }

    public function test_active_user_can_access_authenticated_dashboard(): void
    {
        $response = $this->actingAs($this->activeStudent)->get('/student/dashboard');

        $response->assertStatus(200);
        $this->assertTrue(Auth::check());
    }

    public function test_deactivated_user_is_force_logged_out_on_subsequent_requests(): void
    {
        // 1. Log in the user as active initially
        $this->actingAs($this->activeStudent);
        $this->assertTrue(Auth::check());

        // 2. Simulate deactivation in the background
        $this->activeStudent->update(['is_active' => false]);

        // 3. Make a subsequent request - the CheckUserActive middleware should intercept and log out
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }

    public function test_login_fails_for_deactivated_user(): void
    {
        $response = $this->post('/login', [
            'email' => 'deactivated@clsu.edu.ph',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }

    public function test_secure_session_cookie_configuration(): void
    {
        // Assert secure cookie is configured correctly based on environment checks
        $secureConfig = config('session.secure');
        
        // Since we are in the testing environment, it should evaluate to false
        $this->assertFalse($secureConfig);
    }
}
