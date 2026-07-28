<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\Scholarship;
use App\Models\Setting;
use App\Models\User;
use App\Jobs\ScanDocumentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApplicationAutoApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Scholarship $scholarshipClean;
    private Scholarship $scholarshipCustom;
    private AcademicTerm $term;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'academic_year' => '2026-2027',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Clean program with zero custom fields
        $this->scholarshipClean = Scholarship::create([
            'name' => 'Clean Scholarship',
            'min_gwa_required' => 2.50,
            'status' => 'Active',
        ]);

        // Program requiring custom fields
        $this->scholarshipCustom = Scholarship::create([
            'name' => 'Custom Field Scholarship',
            'min_gwa_required' => 2.50,
            'status' => 'Active',
        ]);
        $this->scholarshipCustom->fields()->create([
            'field_label' => 'Solo Parent ID',
            'field_name' => 'solo_parent_id',
            'field_type' => 'file',
            'is_required' => true,
        ]);

        // Configure system settings for auto-approval
        Setting::set('auto_approval_enabled', '1');
        Setting::set('auto_approval_min_confidence', '95.0');
        Setting::set('auto_approval_max_anomalies', '0');
        Setting::set('ai_fraud_threshold', '50.0');
        Setting::set('gwa_discrepancy_tolerance', '0.01');

        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::disk('local')->put('uploads/test_cog.pdf', 'fake transcript contents');
    }

    /** @test */
    public function it_auto_approves_when_document_is_authentic_and_matches_criteria()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipClean->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipClean->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        // Mock AI Analyzer API response (authentic transcript)
        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 2.00, // 98% confidence
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => [],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        // Execute Scan Job
        (new ScanDocumentJob($app->id))->handle();

        // Assert application status is Auto-Approved
        $this->assertEquals('Approved', $app->fresh()->status);
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $app->id,
            'status' => 'Approved',
            'remarks' => 'Application auto-approved by Smart Verification Engine (Zero anomalies detected).',
        ]);
    }

    /** @test */
    public function it_bypasses_auto_approval_when_confidence_is_below_minimum()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipClean->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipClean->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        // Mock AI response with 88% confidence (12% fraud probability)
        // Min confidence setting is 95.0%
        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 12.00, // 88% confidence
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => [],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        (new ScanDocumentJob($app->id))->handle();

        // Stays pending because it does not meet the minimum confidence threshold
        $this->assertEquals('Pending', $app->fresh()->status);
    }

    /** @test */
    public function it_bypasses_auto_approval_when_document_has_anomaly_flags()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipClean->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipClean->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        // Mock AI response with font inconsistency anomalies
        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 1.00,
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => ['font_inconsistency'],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        (new ScanDocumentJob($app->id))->handle();

        // Stays pending due to anomaly flag
        $this->assertEquals('Pending', $app->fresh()->status);
    }

    /** @test */
    public function it_bypasses_auto_approval_when_scholarship_has_custom_fields()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipCustom->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipCustom->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        // Mock clean authentic response
        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 1.00,
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => [],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        (new ScanDocumentJob($app->id))->handle();

        // Stays pending because the scholarship requires manual verification of custom fields
        $this->assertEquals('Pending', $app->fresh()->status);
    }

    /** @test */
    public function it_bypasses_auto_approval_when_disabled_globally()
    {
        // Disable auto-approval globally
        Setting::set('auto_approval_enabled', '0');

        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipClean->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipClean->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 1.00,
                'classification' => 'authentic',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => [],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        (new ScanDocumentJob($app->id))->handle();

        $this->assertEquals('Pending', $app->fresh()->status);
    }

    /** @test */
    public function it_bypasses_auto_approval_when_classification_is_tampered()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipClean->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarshipClean->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $app->id,
            'file_path' => 'uploads/test_cog.pdf',
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        Http::fake([
            '*/analyze-document*' => Http::response([
                'fraud_probability' => 85.00,
                'classification' => 'tampered',
                'extracted_gwa' => 1.75,
                'anomaly_indicators' => [],
                'paths' => ['heatmap_path' => 'heatmaps/test.png'],
            ], 200)
        ]);

        (new ScanDocumentJob($app->id))->handle();

        $this->assertEquals('Pending', $app->fresh()->status);
    }
}
