<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UiUxHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Test Student',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }

    public function test_health_check_endpoint_returns_healthy_status_json(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'environment',
            'checks' => [
                'database' => [
                    'status',
                    'error',
                ],
                'storage' => [
                    'status',
                    'error',
                ],
            ],
        ]);

        $response->assertJson([
            'status' => 'ok',
            'environment' => 'testing',
            'checks' => [
                'database' => [
                    'status' => 'ok',
                ],
                'storage' => [
                    'status' => 'ok',
                ],
            ],
        ]);
    }

    public function test_page_renders_with_clsu_theme_standardization(): void
    {
        $response = $this->actingAs($this->student)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('data-theme="light"', false);
    }
}
