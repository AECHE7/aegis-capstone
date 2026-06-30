<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RealtimeNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest is redirected.
     */
    public function test_guest_is_redirected_from_notification_stream(): void
    {
        $response = $this->get(route('notifications.stream'));
        $response->assertStatus(302);
    }

    /**
     * Test that authenticated user receives streamed SSE response.
     */
    public function test_authenticated_user_can_access_notification_stream(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Seed a fake notification
        $user->notifications()->create([
            'id' => '11111111-1111-1111-1111-111111111111',
            'type' => 'App\Notifications\NewApplicationNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => ['title' => 'Test', 'message' => 'Test message'],
        ]);

        $response = $this->actingAs($user)->get(route('notifications.stream'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
        $response->assertHeader('Cache-Control', 'no-cache, private');
        $response->assertHeader('Connection', 'keep-alive');

        // Capture and assert streamed contents
        $content = $response->streamedContent();
        $this->assertStringContainsString('data:', $content);
        $this->assertStringContainsString('"count":1', $content);
        $this->assertStringContainsString('"refresh":true', $content);
    }
}
