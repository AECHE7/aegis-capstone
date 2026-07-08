<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;

class ScheduledAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_filters_out_future_scheduled_announcements()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 1. Immediately visible announcement
        $visibleAnn = Announcement::create([
            'title' => 'Assembly Today',
            'content' => 'Mandatory meeting at 2 PM.',
            'author_id' => $admin->id,
            'scheduled_publish_at' => now()->subMinutes(10),
        ]);

        // 2. Future scheduled announcement
        $futureAnn = Announcement::create([
            'title' => 'Future Announcement',
            'content' => 'This announcement will go live tomorrow.',
            'author_id' => $admin->id,
            'scheduled_publish_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Assembly Today');
        $response->assertDontSee('Future Announcement');
    }

    public function test_student_dashboard_filters_out_expired_scheduled_announcements()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 1. Active visible announcement
        $activeAnn = Announcement::create([
            'title' => 'Active Announcement',
            'content' => 'This is currently active.',
            'author_id' => $admin->id,
            'scheduled_publish_at' => now()->subMinutes(10),
            'scheduled_delete_at' => now()->addHours(2),
        ]);

        // 2. Expired announcement
        $expiredAnn = Announcement::create([
            'title' => 'Expired Announcement',
            'content' => 'This announcement expired an hour ago.',
            'author_id' => $admin->id,
            'scheduled_publish_at' => now()->subHours(5),
            'scheduled_delete_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Active Announcement');
        $response->assertDontSee('Expired Announcement');
    }
}
