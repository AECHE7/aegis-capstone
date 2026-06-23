<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\StaffInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StaffInvitationTest extends TestCase
{
    use RefreshDatabase;

    private $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Super Admin user
        $this->superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_superadmin_can_access_staff_list_page(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.staff'));

        $response->assertStatus(200);
        $response->assertSee('Staff Management');
        $response->assertSee('Invite Staff');
    }

    public function test_superadmin_can_invite_staff_with_valid_clsu_email(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.staff.invite'), [
                'name' => 'Jane Staff',
                'email' => 'janestaff@clsu2.edu.ph',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Assert user record is created in database with 'admin' role and null verified timestamp
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Staff',
            'email' => 'janestaff@clsu2.edu.ph',
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $user = User::where('email', 'janestaff@clsu2.edu.ph')->first();
        $this->assertNotNull($user);

        // Assert invitation record is created
        $this->assertDatabaseHas('user_invitations', [
            'user_id' => $user->id,
        ]);

        $invitation = UserInvitation::where('user_id', $user->id)->first();
        $this->assertNotNull($invitation->token);
        $this->assertTrue($invitation->expires_at->isFuture());

        // Assert email is logged in email_logs
        $this->assertDatabaseHas('email_logs', [
            'recipient' => 'janestaff@clsu2.edu.ph',
            'subject' => '[A.E.G.I.S.] Staff Account Invitation',
        ]);
    }

    public function test_superadmin_cannot_invite_staff_with_non_clsu_email(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.staff.invite'), [
                'name' => 'External Staff',
                'email' => 'external@gmail.com',
            ]);

        $response->assertSessionHasErrors('email');

        // Assert no user is created
        $this->assertDatabaseMissing('users', [
            'email' => 'external@gmail.com',
        ]);
    }

    public function test_invited_staff_can_view_activation_form_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Jane Staff',
            'email' => 'janestaff@clsu.edu.ph',
            'password' => bcrypt('placeholder'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $invitation = UserInvitation::create([
            'user_id' => $user->id,
            'token' => 'valid-secure-token-123',
            'expires_at' => now()->addDays(3),
        ]);

        $response = $this->get(route('activate.form', ['token' => 'valid-secure-token-123']));

        $response->assertStatus(200);
        $response->assertSee('Activate Account');
        $response->assertSee('Jane Staff');
        $response->assertSee('janestaff@clsu.edu.ph');
    }

    public function test_invited_staff_cannot_view_activation_form_with_invalid_token(): void
    {
        $response = $this->get(route('activate.form', ['token' => 'invalid-token']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_invited_staff_cannot_view_activation_form_with_expired_token(): void
    {
        $user = User::create([
            'name' => 'Jane Staff',
            'email' => 'janestaff@clsu.edu.ph',
            'password' => bcrypt('placeholder'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $invitation = UserInvitation::create([
            'user_id' => $user->id,
            'token' => 'expired-token-123',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('activate.form', ['token' => 'expired-token-123']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_invited_staff_can_activate_account_and_set_password(): void
    {
        $user = User::create([
            'name' => 'Jane Staff',
            'email' => 'janestaff@clsu.edu.ph',
            'password' => bcrypt('placeholder'),
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $invitation = UserInvitation::create([
            'user_id' => $user->id,
            'token' => 'valid-token-to-activate',
            'expires_at' => now()->addDays(3),
        ]);

        $response = $this->post(route('activate.submit'), [
            'token' => 'valid-token-to-activate',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Assert user password is updated and user is verified
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertNotNull($user->email_verified_at);

        // Assert invitation token is deleted
        $this->assertDatabaseMissing('user_invitations', [
            'token' => 'valid-token-to-activate',
        ]);

        // Assert user is logged in
        $this->assertAuthenticatedAs($user);
    }
}
