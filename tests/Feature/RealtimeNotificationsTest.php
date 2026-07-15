<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RealtimeNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest is redirected from notifications endpoint.
     */
    public function test_guest_is_redirected_from_notifications(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertStatus(302);
    }

    /**
     * Test that authenticated user can poll notifications via JSON.
     */
    public function test_authenticated_user_can_access_notifications(): void
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

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications' => [
                '*' => [
                    'id',
                    'title',
                    'message',
                    'created_at'
                ]
            ],
            'count'
        ]);

        $response->assertJsonFragment([
            'count' => 1,
            'title' => 'Test',
            'message' => 'Test message'
        ]);
    }
}
