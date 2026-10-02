<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Announcement;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\ApplicationSubmissionConfirmationNotification;
use App\Notifications\BroadcastNotification;
use App\Notifications\NewAnnouncementNotification;
use App\Notifications\NewApplicationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class NotificationComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_receives_submission_confirmation_and_status_notification(): void
    {
        $term = \App\Models\AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $scholarship = Scholarship::create([
            'name' => 'City Academic Merit',
            'description' => 'Academic merit grant',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $term->id,
            'program_name' => $scholarship->name,
            'status' => 'Pending Verification',
            'gwa' => 1.75,
        ]);

        // 1. Send submission confirmation
        $student->notify(new ApplicationSubmissionConfirmationNotification($application));

        $this->assertEquals(1, $student->unreadNotifications()->count());
        $notif = $student->notifications()->first();
        $this->assertEquals('Application Submitted Successfully', $notif->data['title']);
        $this->assertStringContainsString('APP-' . $application->id, $notif->data['message']);

        // 2. Send status update notification (e.g. Approved)
        $application->status = 'Approved';
        $application->remarks = 'All criteria met.';
        $student->notify(new ApplicationStatusNotification($application));

        $this->assertEquals(2, $student->unreadNotifications()->count());
        $statusNotif = $student->notifications()->where('type', ApplicationStatusNotification::class)->first();
        $this->assertNotNull($statusNotif);
        $this->assertEquals('Application Status Update', $statusNotif->data['title']);
        $this->assertStringContainsString('Approved', $statusNotif->data['message']);
    }

    public function test_staff_and_admin_receive_new_application_notification(): void
    {
        $term = \App\Models\AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);
        $staff = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'name' => 'Juan Dela Cruz']);
        $scholarship = Scholarship::create([
            'name' => 'College Entrance Scholarship',
            'description' => 'Entrance scholarship grant',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $term->id,
            'program_name' => $scholarship->name,
            'status' => 'Pending Verification',
            'gwa' => 1.50,
        ]);

        $staff->notify(new NewApplicationNotification($application));

        $this->assertEquals(1, $staff->unreadNotifications()->count());
        $notif = $staff->notifications()->first();
        $this->assertEquals('New Application Submitted', $notif->data['title']);
        $this->assertStringContainsString('Juan Dela Cruz', $notif->data['message']);
        $this->assertEquals(route('admin.review', $application->id), $notif->data['url']);
    }

    public function test_all_users_receive_announcement_and_broadcast_notifications(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $staff = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $director = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);

        $announcement = Announcement::create([
            'title' => 'Midterm Scholarship Verification Deadline',
            'content' => 'Please upload all requirements by Friday.',
            'author_id' => $director->id,
        ]);

        // Dispatch announcement to all users
        \Illuminate\Support\Facades\Notification::send([$student, $staff, $director], new NewAnnouncementNotification($announcement));

        $this->assertEquals(1, $student->unreadNotifications()->count());
        $this->assertEquals(1, $staff->unreadNotifications()->count());
        $this->assertEquals(1, $director->unreadNotifications()->count());

        $this->assertEquals('Official Announcement', $student->notifications()->first()->data['title']);
        $this->assertEquals(route('student.announcements'), $student->notifications()->first()->data['url']);
        $this->assertEquals(route('admin.announcements.index'), $staff->notifications()->first()->data['url']);

        // Dispatch campus broadcast
        \Illuminate\Support\Facades\Notification::send([$student, $staff, $director], new BroadcastNotification('Emergency System Maintenance', 'The server will be rebooted tonight at 11 PM.'));

        $this->assertEquals(2, $student->unreadNotifications()->count());
        $this->assertEquals(2, $staff->unreadNotifications()->count());
        $this->assertEquals(2, $director->unreadNotifications()->count());
    }

    public function test_notifications_api_lifecycle(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $announcement = Announcement::create([
            'title' => 'Portal Maintenance Notice',
            'content' => 'Scheduled maintenance.',
            'author_id' => $user->id,
        ]);

        $user->notify(new NewAnnouncementNotification($announcement));

        // 1. Fetch via GET /notifications
        $response = $this->actingAs($user)->getJson('/notifications');
        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('unread_count', 1)
            ->assertJsonStructure([
                'notifications' => [
                    '*' => ['id', 'title', 'message', 'url', 'is_read', 'created_at']
                ],
                'count',
                'unread_count'
            ]);

        $notifId = $user->notifications()->first()->id;

        // 2. Mark as read via POST /notifications/{id}/read
        $readResponse = $this->actingAs($user)->postJson("/notifications/{$notifId}/read");
        $readResponse->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(0, $user->unreadNotifications()->count());

        // 3. Mark as unread via POST /notifications/{id}/unread
        $unreadResponse = $this->actingAs($user)->postJson("/notifications/{$notifId}/unread");
        $unreadResponse->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(1, $user->unreadNotifications()->count());

        // 4. Clear all via POST /notifications/clear
        $clearResponse = $this->actingAs($user)->postJson('/notifications/clear');
        $clearResponse->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(0, $user->unreadNotifications()->count());

        // 5. Delete notification via DELETE /notifications/{id}
        $deleteResponse = $this->actingAs($user)->deleteJson("/notifications/{$notifId}");
        $deleteResponse->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(0, $user->notifications()->count());
    }

    public function test_notifications_center_html_view_and_bulk_actions(): void
    {
        $user = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $announcement1 = Announcement::create(['title' => 'Deadline 1', 'content' => 'Content 1', 'author_id' => $user->id]);
        $announcement2 = Announcement::create(['title' => 'Deadline 2', 'content' => 'Content 2', 'author_id' => $user->id]);

        $user->notify(new NewAnnouncementNotification($announcement1));
        $user->notify(new NewAnnouncementNotification($announcement2));

        // 1. Visit HTML page
        $htmlResponse = $this->actingAs($user)->get('/notifications?view=center');
        $htmlResponse->assertOk();
        $htmlResponse->assertSee('Notifications & Alerts Center');
        $htmlResponse->assertSee('Delivery Preferences');

        $ids = $user->notifications()->pluck('id')->toArray();

        // 2. Bulk mark read
        $bulkReadResponse = $this->actingAs($user)->postJson('/notifications/bulk', [
            'action' => 'mark_read',
            'ids' => $ids,
        ]);
        $bulkReadResponse->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(0, $user->unreadNotifications()->count());

        // 3. Bulk mark unread
        $bulkUnreadResponse = $this->actingAs($user)->postJson('/notifications/bulk', [
            'action' => 'mark_unread',
            'ids' => $ids,
        ]);
        $bulkUnreadResponse->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(2, $user->unreadNotifications()->count());

        // 4. Bulk delete
        $bulkDeleteResponse = $this->actingAs($user)->postJson('/notifications/bulk', [
            'action' => 'delete',
            'ids' => [$ids[0]],
        ]);
        $bulkDeleteResponse->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(1, $user->notifications()->count());
    }

    public function test_user_can_save_notification_preferences_and_send_test_alert(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Update preferences
        $prefResponse = $this->actingAs($user)->postJson('/notifications/preferences', [
            'in_app' => true,
            'applications' => true,
            'announcements' => false,
            'broadcasts' => true,
            'email' => false,
        ]);
        $prefResponse->assertOk()->assertJson(['success' => true]);

        $user->refresh();
        $prefs = $user->getNotificationPreferences();
        $this->assertTrue($prefs['applications']);
        $this->assertFalse($prefs['announcements']);
        $this->assertFalse($prefs['email']);

        // 2. Send test notification
        $testResponse = $this->actingAs($user)->postJson('/notifications/test');
        $testResponse->assertOk()->assertJson(['success' => true]);

        $this->assertEquals(1, $user->notifications()->count());
        $this->assertEquals('Broadcast: Dynamic Notification Verification', $user->notifications()->first()->data['title']);
    }

    public function test_notification_urls_are_sanitized_to_relative_paths(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create a database notification with hardcoded localhost URL
        $user->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\NewApplicationNotification',
            'data' => [
                'type' => 'new_application',
                'title' => 'New Application APP-999',
                'message' => 'New application received.',
                'url' => 'http://localhost/admin/review/999',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/notifications');
        $response->assertOk();
        $notifs = $response->json('notifications');

        $this->assertCount(1, $notifs);
        $this->assertEquals('/admin/review/999', $notifs[0]['url']);

        // Check HTML page renders bootstrap-5 pagination and not Tailwind giant icons
        $htmlResponse = $this->actingAs($user)->get('/notifications?view=center');
        $htmlResponse->assertOk();
        $htmlResponse->assertSee('Open & View', false);
    }

    public function test_topbar_and_sidebar_notification_badge_renders_correctly_when_unread_notifications_exist(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Add 3 unread notifications
        for ($i = 1; $i <= 3; $i++) {
            $admin->notifications()->create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\NewApplicationNotification',
                'data' => [
                    'type' => 'new_application',
                    'title' => "Application $i",
                    'message' => "Message $i",
                    'url' => "/admin/review/$i",
                ],
                'read_at' => null,
                'created_at' => now(),
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        // 1. Topbar bell button must have overflow: visible
        $response->assertSee('id="notifBellBtn"', false);
        $response->assertSee('overflow: visible !important;', false);

        // 2. Bell badge must display the unread count server-side without being hidden
        $response->assertSee('id="notifBadge"', false);
        $response->assertSee('3', false);
        $response->assertDontSee('<span class="position-absolute badge rounded-pill bg-danger border border-2 border-white d-none" id="notifBadge"', false);

        // 3. Redundant sidebar notifications link removed to keep sidebar minimal and avoid duplication with topbar bell
        $response->assertDontSee('sidebar-unread-badge', false);
        $response->assertDontSee('sidebar-collapsed-dot', false);
    }
}

