<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\Document;
use App\Models\Application;
use App\Models\AIResult;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Jobs\ScanDocumentJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $admin;
    protected $student;
    protected $scholarship;
    protected $term;
    protected $application;
    protected $document;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $this->superadmin = User::factory()->create(['role' => 'superadmin', 'email_verified_at' => now()]);
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);

        // Seed default settings
        Setting::set('app_name', 'A.E.G.I.S.');
        Setting::set('university_name', 'Central Luzon State University');
        Setting::set('ai_fraud_threshold', '50.0');
        Setting::set('gwa_discrepancy_tolerance', '0.01');

        // Create standard application resources
        $this->scholarship = Scholarship::create(['name' => 'Test Scholarship', 'min_gwa_required' => 2.0, 'status' => 'Active']);
        $this->term = AcademicTerm::create(['semester' => '1st Semester', 'academic_year' => '2025-2026', 'is_active' => true]);

        $this->application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        $filePath = 'uploads/test_doc.pdf';
        Storage::fake('local');
        Storage::disk('local')->put($filePath, 'fake content');

        $this->document = Document::create([
            'application_id' => $this->application->id,
            'file_path' => $filePath,
            'original_name' => 'test_doc.pdf',
            'document_type' => 'COG'
        ]);
    }

    /** @test */
    public function setting_helper_gets_and_sets_data_and_uses_cache()
    {
        // Get value
        $this->assertEquals('A.E.G.I.S.', Setting::get('app_name'));

        // Update value
        Setting::set('app_name', 'New Portal Name');

        // Check value
        $this->assertEquals('New Portal Name', Setting::get('app_name'));

        // Retrieve default fallback if not exists
        $this->assertEquals('Fallback Value', Setting::get('non_existent_key', 'Fallback Value'));
    }

    /** @test */
    public function superadmin_can_access_settings_page_and_update_settings()
    {
        Storage::fake('local');

        $response = $this->actingAs($this->superadmin)
                         ->get(route('superadmin.settings'));

        $response->assertStatus(200);
        $response->assertSee('System Settings');
        $response->assertSee('A.E.G.I.S.');

        // Test updating values
        // Use create() instead of image() to avoid GD extension dependency
        $newLogo = UploadedFile::fake()->create('custom_logo.png', 100);

        $updateResponse = $this->actingAs($this->superadmin)
                               ->post(route('superadmin.settings.update'), [
                                   'app_name' => 'Custom Portal',
                                   'university_name' => 'Custom State College',
                                   'ai_fraud_threshold' => 75.5,
                                   'gwa_discrepancy_tolerance' => 0.05,
                                   'app_logo' => $newLogo,
                               ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('Custom Portal', Setting::get('app_name'));
        $this->assertEquals('Custom State College', Setting::get('university_name'));
        $this->assertEquals(75.5, Setting::get('ai_fraud_threshold'));
        $this->assertEquals(0.05, Setting::get('gwa_discrepancy_tolerance'));
        $this->assertNotNull(Setting::get('app_logo'));
    }

    /** @test */
    public function non_superadmins_cannot_access_or_update_settings()
    {
        // Admin
        $this->actingAs($this->admin)
             ->get(route('superadmin.settings'))
             ->assertStatus(403);

        $this->actingAs($this->admin)
             ->post(route('superadmin.settings.update'), ['app_name' => 'Hacked'])
             ->assertStatus(403);

        // Student
        $this->actingAs($this->student)
             ->get(route('superadmin.settings'))
             ->assertStatus(403);
    }

    /** @test */
    public function scan_document_job_uses_default_ai_settings()
    {
        // GWA matches exactly (1.75 declared, 1.75 extracted).
        // Fraud score is 45.0%. Default threshold is 50.0%, so classified as 'authentic'.
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'fraud_probability' => 45.0,
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'paths' => [
                    'heatmap_path' => 'heatmap_outputs/test_heatmap.jpg',
                ]
            ], 200)
        ]);

        (new ScanDocumentJob($this->application->id))->handle();

        $aiResult = AIResult::where('document_id', $this->document->id)->first();
        $this->assertEquals('authentic', $aiResult->classification);
        $this->assertEquals(45.0, $aiResult->fraud_probability);
    }

    /** @test */
    public function scan_document_job_uses_custom_ai_fraud_threshold()
    {
        // Set AI Threshold to 30.0%. Since 45.0% >= 30.0%, it should flag as 'tampered'.
        Setting::set('ai_fraud_threshold', '30.0');

        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'fraud_probability' => 45.0,
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'paths' => [
                    'heatmap_path' => 'heatmap_outputs/test_heatmap.jpg',
                ]
            ], 200)
        ]);

        (new ScanDocumentJob($this->application->id))->handle();

        $aiResult = AIResult::where('document_id', $this->document->id)->first();
        $this->assertEquals('tampered', $aiResult->classification);
    }

    /** @test */
    public function scan_document_job_uses_custom_gwa_tolerance()
    {
        // Let's set tolerance to 0.05. Mismatched GWA of 1.85 (diff is 0.10) exceeds tolerance.
        Setting::set('gwa_discrepancy_tolerance', '0.05');

        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'fraud_probability' => 15.0,
                'classification' => 'authentic',
                'extracted_gwa' => 1.85,
                'paths' => [
                    'heatmap_path' => 'heatmap_outputs/test_heatmap.jpg',
                ]
            ], 200)
        ]);

        (new ScanDocumentJob($this->application->id))->handle();

        $aiResult = AIResult::where('document_id', $this->document->id)->first();
        $this->assertEquals(99.00, $aiResult->fraud_probability);
        $this->assertEquals('Tampered (Grade Discrepancy)', $aiResult->classification);
    }

    /** @test */
    public function database_hard_reset_route_wipes_all_students_and_applications()
    {
        $response = $this->get(route('system.reset-uat'));
        $response->assertStatus(200);
        $response->assertSee('Staging database reset successfully!');

        $this->assertEquals(0, User::where('role', 'student')->count());
        $this->assertEquals(0, Application::count());
    }
}
