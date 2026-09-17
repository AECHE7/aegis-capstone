<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class AutoWakeAITest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'director@clsu.edu.ph',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function ai_wake_endpoint_returns_json_response()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'healthy', 'model_loaded' => true], 200),
        ]);

        $response = $this->get(route('ai.wake'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 200,
        ]);
    }

    #[Test]
    public function scheduler_run_warms_up_ai_and_closes_expired_scholarships()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'healthy'], 200),
        ]);

        $validKey = config('services.scheduler.key', 'aegis_cron_secret');
        $response = $this->get("/scheduler/run?key={$validKey}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Scheduler run complete.',
            'ai_status' => 'healthy',
        ]);
    }

    #[Test]
    public function superadmin_can_check_ai_status_endpoint()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'healthy', 'pipeline_version' => 'V2'], 200),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.settings.ai-status'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'online',
        ]);
    }

    #[Test]
    public function superadmin_settings_displays_ai_health_and_wake_hub()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.settings'));

        $response->assertStatus(200);
        $response->assertSee('AI Microservice Container Health', false);
        $response->assertSee('Wake Up AI', false);
        $response->assertSee('aiLiveBadge', false);
        $response->assertSee('How to Keep AI Awake 24/7 Automatically', false);
    }

    #[Test]
    public function superadmin_can_trigger_wake_ai_action()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'healthy'], 200),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.settings.wake-ai'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    #[Test]
    public function artisan_wake_ai_command_executes_successfully()
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'healthy', 'version' => '2.1.0'], 200),
        ]);

        $this->artisan('aegis:wake-ai --retries=1')
            ->assertExitCode(0);
    }
}
