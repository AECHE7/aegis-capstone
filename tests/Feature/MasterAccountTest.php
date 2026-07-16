<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\MasterTransfer;
use Illuminate\Support\Facades\Session;

class MasterAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $masterUser;
    private User $regularUser;
    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure Setting app_name exists
        Setting::set('app_name', 'A.E.G.I.S.');
        Setting::set('master_email', 'gadianoriel07@gmail.com');

        // Create Master user
        $this->masterUser = User::create([
            'name' => 'Master User',
            'email' => 'gadianoriel07@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create target user for transfer
        $this->targetUser = User::create([
            'name' => 'Target User',
            'email' => 'target@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_login_redirects_master_user_to_gateway(): void
    {
        // Bypass MFA check for tests using config settings or mock
        Setting::set('mfa_enforcement', 'none');

        $response = $this->post('/login', [
            'email' => 'gadianoriel07@gmail.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('master.gateway'));
    }

    public function test_login_redirects_regular_user_to_role_dashboard(): void
    {
        Setting::set('mfa_enforcement', 'none');

        $response = $this->post('/login', [
            'email' => 'regular@clsu.edu.ph',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_non_master_user_cannot_access_gateway(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('master.gateway'));

        $response->assertStatus(403);
    }

    public function test_master_user_can_access_gateway(): void
    {
        $response = $this->actingAs($this->masterUser)->get(route('master.gateway'));

        $response->assertStatus(200);
        $response->assertSee('Master Gateway');
        $response->assertSee('Select Active Workspace Role');
    }

    public function test_master_user_can_switch_roles(): void
    {
        // Switch to student
        $response = $this->actingAs($this->masterUser)->post(route('master.switch-role'), [
            'role' => 'student'
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertEquals('student', session('active_role'));
        $this->assertEquals('student', $this->masterUser->fresh()->role);

        // Switch to admin
        $response = $this->actingAs($this->masterUser)->post(route('master.switch-role'), [
            'role' => 'admin'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals('admin', session('active_role'));
        $this->assertEquals('admin', $this->masterUser->fresh()->role);
    }

    public function test_non_master_user_cannot_switch_roles(): void
    {
        $response = $this->actingAs($this->regularUser)->post(route('master.switch-role'), [
            'role' => 'superadmin'
        ]);

        $response->assertStatus(403);
    }

    public function test_master_user_can_initiate_transfer(): void
    {
        $response = $this->actingAs($this->masterUser)->post(route('master.transfer'), [
            'recipient_email' => 'target@clsu.edu.ph'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('master_transfers', [
            'sender_email' => 'gadianoriel07@gmail.com',
            'recipient_email' => 'target@clsu.edu.ph',
            'accepted_at' => null
        ]);
    }

    public function test_unauthenticated_visitor_gets_redirected_with_token_on_accept(): void
    {
        $transfer = MasterTransfer::create([
            'sender_email' => 'gadianoriel07@gmail.com',
            'recipient_email' => 'target@clsu.edu.ph',
            'token' => 'test-token-123',
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->get(route('master.accept-transfer', ['token' => 'test-token-123']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('pending_master_transfer_token', 'test-token-123');
    }

    public function test_non_recipient_cannot_accept_transfer(): void
    {
        $transfer = MasterTransfer::create([
            'sender_email' => 'gadianoriel07@gmail.com',
            'recipient_email' => 'target@clsu.edu.ph',
            'token' => 'test-token-123',
            'expires_at' => now()->addHours(24),
        ]);

        // Attempting to accept as regularUser (regular@clsu.edu.ph)
        $response = $this->actingAs($this->regularUser)->get(route('master.accept-transfer', ['token' => 'test-token-123']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_recipient_can_accept_and_activate_transfer(): void
    {
        $transfer = MasterTransfer::create([
            'sender_email' => 'gadianoriel07@gmail.com',
            'recipient_email' => 'target@clsu.edu.ph',
            'token' => 'test-token-123',
            'expires_at' => now()->addHours(24),
        ]);

        // Show confirmation page
        $response = $this->actingAs($this->targetUser)->get(route('master.accept-transfer', ['token' => 'test-token-123']));
        $response->assertStatus(200);
        $response->assertSee('Verify Master Privilege Transfer');

        // Post confirmation
        $response = $this->actingAs($this->targetUser)->post(route('master.accept-transfer', ['token' => 'test-token-123']));
        
        $response->assertRedirect(route('master.gateway'));
        $this->assertEquals('target@clsu.edu.ph', Setting::get('master_email'));
        $this->assertNotNull($transfer->fresh()->accepted_at);

        // Verify old master is revoked
        $this->assertFalse($this->masterUser->fresh()->isMaster());
        $this->assertTrue($this->targetUser->fresh()->isMaster());
    }
}
