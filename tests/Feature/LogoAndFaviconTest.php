<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Setting;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LogoAndFaviconTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.evaluator@clsu.edu.ph',
            'name' => 'Admin Staff Evaluator',
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'email' => 'student.scholar@clsu2.edu.ph',
            'name' => 'John Andrei Carillo',
        ]);

        $term = AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $scholarship = Scholarship::create([
            'name' => 'CLSU Gender and Development Financial Assistance Program',
            'description' => 'Test Grant',
            'deadline' => now()->addDays(30),
            'status' => 'open',
            'quota' => 50,
        ]);

        $this->admin->scholarships()->attach($scholarship->id);

        $this->application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $term->id,
            'status' => 'submitted',
            'program_name' => 'CLSU Gender and Development Financial Assistance Program',
        ]);
    }

    #[Test]
    public function test_system_logo_route_returns_fallback_image_when_app_logo_is_empty(): void
    {
        Setting::set('app_logo', null);

        $response = $this->get(route('system.logo'));

        $response->assertStatus(200);
        $this->assertStringContainsString('image/png', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function test_system_logo_route_falls_back_when_app_logo_file_does_not_exist_in_storage(): void
    {
        // Simulate ephemeral container wipe where setting exists in DB but not on disk
        Setting::set('app_logo', 'settings/ephemeral_missing_logo_123.png');

        $response = $this->get(route('system.logo'));

        $response->assertStatus(200);
        $this->assertStringContainsString('image/png', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function test_get_logo_url_returns_reliable_asset_path_when_app_logo_missing_or_ephemeral(): void
    {
        Setting::set('app_logo', null);
        $logoUrl = Setting::getLogoUrl();
        $this->assertNotEmpty($logoUrl);
        $this->assertTrue(str_contains($logoUrl, 'clsu-seal.png') || str_contains($logoUrl, 'logo.png'));

        // Test with non-existent storage path
        Setting::set('app_logo', 'ghost/missing_seal.png');
        $logoUrl2 = Setting::getLogoUrl();
        $this->assertNotEmpty($logoUrl2);
        $this->assertTrue(str_contains($logoUrl2, 'clsu-seal.png') || str_contains($logoUrl2, 'logo.png'));
    }

    #[Test]
    public function test_favicon_ico_file_exists_and_is_valid_binary(): void
    {
        $faviconPath = public_path('favicon.ico');
        $this->assertFileExists($faviconPath);
        $this->assertGreaterThan(1000, filesize($faviconPath), 'favicon.ico must not be empty or 0 bytes');
    }

    #[Test]
    public function test_authenticated_app_layout_renders_universal_favicons(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('rel="icon" type="image/x-icon"', false);
        $response->assertSee('favicon.ico', false);
        $response->assertSee('clsu-seal.png', false);
        $response->assertSee('rel="apple-touch-icon"', false);
    }

    #[Test]
    public function test_guest_views_render_universal_favicons(): void
    {
        $views = ['/login', '/register', '/forgot-password', '/scholarships'];

        foreach ($views as $view) {
            $response = $this->get($view);
            $response->assertStatus(200);
            $response->assertSee('rel="icon" type="image/x-icon"', false);
            $response->assertSee('favicon.ico', false);
            $response->assertSee('clsu-seal.png', false);
        }
    }

    #[Test]
    public function test_sidebar_brand_logo_renders_with_onerror_fallback(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.applicant-forms.index'));

        $response->assertStatus(200);
        $response->assertSee('sidebar-brand-icon', false);
        $response->assertSee('onerror="this.onerror=null; this.src=', false);
        $response->assertSee('clsu-seal.png', false);
    }

    #[Test]
    public function test_applicant_form_preview_contains_dual_seals_with_onerror_fallbacks(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/admin/applications/{$this->application->id}/preview-form");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $html = $response->json('html');
        $this->assertStringContainsString('clsu-seal.png', $html);
        $this->assertStringContainsString('osa-seal.png', $html);
        $this->assertStringContainsString('onerror="this.onerror=null;', $html);
        $this->assertStringContainsString('applicant-form-sheet', $html);
    }
}
