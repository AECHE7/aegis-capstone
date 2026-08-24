<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;

class AnnouncementBoardTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_publish_announcement_and_notify_students(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.announcements.store'), [
            'title' => 'Important Deadline Update',
            'content' => 'Please submit all GAD requirements by Friday noon.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify announcement is created in database
        $this->assertDatabaseHas('announcements', [
            'title' => 'Important Deadline Update',
            'content' => 'Please submit all GAD requirements by Friday noon.',
            'author_id' => $this->admin->id,
        ]);

        // Verify student user received database notification
        $this->assertEquals(1, $this->student->notifications()->count());
        $notification = $this->student->notifications()->first();
        $this->assertEquals('Important Deadline Update', $notification->data['message']);
        $this->assertEquals('announcement', $notification->data['type']);
    }

    public function test_student_cannot_publish_announcement(): void
    {
        $response = $this->actingAs($this->student)->postJson(route('admin.announcements.store'), [
            'title' => 'Student Announcement Attempt',
            'content' => 'Students trying to broadcast content.',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_announcements_are_listed_on_student_dashboard(): void
    {
        $announcement = Announcement::create([
            'title' => 'Portal Maintenance Alert',
            'content' => 'The AEGIS portal will be offline for maintenance on Sunday.',
            'author_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Maintenance Alert');
        $response->assertSee('The AEGIS portal will be offline for maintenance on Sunday.');
    }
}
