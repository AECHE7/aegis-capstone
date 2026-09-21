<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Scholarship;
use App\Notifications\NewAnnouncementNotification;
use App\Notifications\BroadcastNotification;

class BroadcastNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'name' => 'Director SuperAdmin',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Student Scholar',
            'email' => 'scholar@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);
    }

    public function test_superadmin_can_broadcast_to_all_users_and_create_in_app_notifications(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.broadcast.send'), [
            'title' => 'Urgent Campus Advisory',
            'body' => 'Classes and office transactions are suspended tomorrow.',
            'target' => 'all_users',
        ]);

        $response->assertSessionHas('success');

        // Verify that in-app database notifications were dispatched to every active user
        $this->assertGreaterThanOrEqual(1, $this->student->notifications()->count());
        $this->assertGreaterThanOrEqual(1, $this->admin->notifications()->count());
        $this->assertGreaterThanOrEqual(1, $this->superadmin->notifications()->count());

        $studentNotif = $this->student->notifications()->first();
        $this->assertEquals('broadcast', $studentNotif->data['type']);
        $this->assertStringContainsString('Urgent Campus Advisory', $studentNotif->data['title']);
    }

    public function test_announcement_publishing_notifies_all_active_portal_users(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.announcements.store'), [
            'title' => 'Midterm Stipend Release Schedule',
            'content' => 'Stipend disbursement starts next Monday at the Cashier Office.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Both student and admin receive notifications
        $this->assertGreaterThanOrEqual(1, $this->student->notifications()->count());
        $this->assertGreaterThanOrEqual(1, $this->admin->notifications()->count());
    }

    public function test_new_announcement_notification_provides_role_aware_url(): void
    {
        $announcement = Announcement::create([
            'title' => 'Sample Announcement',
            'content' => 'Sample content',
            'author_id' => $this->admin->id,
        ]);

        $notif = new NewAnnouncementNotification($announcement);

        $studentData = $notif->toArray($this->student);
        $this->assertEquals(route('student.announcements'), $studentData['url']);

        $adminData = $notif->toArray($this->admin);
        $this->assertEquals(route('admin.announcements.index'), $adminData['url']);

        $superadminData = $notif->toArray($this->superadmin);
        $this->assertEquals(route('admin.announcements.index'), $superadminData['url']);
    }

    public function test_user_with_incomplete_profile_can_poll_and_read_notifications(): void
    {
        // Delete student profile to make it incomplete
        $this->student->profile()?->delete();
        $this->assertFalse($this->student->isProfileComplete());

        // Create a notification for this student
        $this->student->notify(new BroadcastNotification('Welcome', 'Please complete your profile.'));

        // Notification fetch should return 200 JSON, not 403
        $response = $this->actingAs($this->student)->getJson(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['notifications', 'count']);
        $this->assertGreaterThanOrEqual(1, $response->json('count'));

        // Marking as read should return 200
        $notifId = $this->student->unreadNotifications()->first()->id;
        $readResponse = $this->actingAs($this->student)->postJson(route('notifications.read', $notifId));
        $readResponse->assertStatus(200);
    }
}
