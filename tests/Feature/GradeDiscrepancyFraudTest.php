<?php

namespace Tests\Feature;

use App\Jobs\ScanDocumentJob;
use App\Models\AIResult;
use App\Models\Application;
use App\Models\Document;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GradeDiscrepancyFraudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: build a student application with a document and mock AI response.
     */
    private function buildScenario(string $declaredGwa, float $extractedGwa, float $aiBase = 12.50): array
    {
        Storage::fake('local');

        $student = User::create([
            'name'     => 'Test Student',
            'email'    => 'student' . rand(1000, 9999) . '@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role'     => 'student'
        ]);

        $scholarship = Scholarship::create([
            'name'             => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status'           => 'Active'
        ]);

        $application = Application::create([
            'user_id'       => $student->id,
            'scholarship_id'=> $scholarship->id,
            'program_name'  => $scholarship->name,
            'gwa'           => $declaredGwa,
            'status'        => 'Pending'
        ]);

        $filePath = 'uploads/test_cog.pdf';
        Storage::disk('local')->put($filePath, 'fake pdf content');

        $document = Document::create([
            'application_id' => $application->id,
            'file_path'      => $filePath,
            'original_name'  => 'test_cog.pdf',
            'document_type'  => 'COG'
        ]);

        Http::fake([
            '*' => Http::response([
                'status'           => 'success',
                'fraud_probability'=> $aiBase,
                'classification'   => 'Authentic',
                'extracted_gwa'    => $extractedGwa,
                'paths'            => ['heatmap_path' => 'heatmap_outputs/test.jpg'],
            ], 200)
        ]);

        return [$application, $document];
    }

    #[Test]
    public function it_keeps_original_ai_prediction_when_gwa_matches()
    {
        [$application, $document] = $this->buildScenario('1.75', 1.75, 12.50);

        (new ScanDocumentJob($application->id))->handle();

        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);
        $this->assertEquals(12.50, $aiResult->fraud_probability);
        $this->assertEquals('Authentic', $aiResult->classification);
    }

    #[Test]
    public function it_flags_minor_ocr_variance_as_review_needed_not_99_percent()
    {
        // Diff = 0.03 (≤ 0.05) — Tesseract digit misread. Must NOT become 99%.
        [$application, $document] = $this->buildScenario('1.75', 1.78, 12.50);

        (new ScanDocumentJob($application->id))->handle();

        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);

        // Should be raised to at least 35% (Review Needed range) but NOT 99%
        $this->assertGreaterThanOrEqual(35.0, $aiResult->fraud_probability);
        $this->assertLessThan(70.0, $aiResult->fraud_probability);
        $this->assertEquals('Review Needed (Minor Grade Variance)', $aiResult->classification);

        // Anomaly indicator must record exact values
        $this->assertContains('gwa_minor_variance:declared_1.75_vs_extracted_1.78', $aiResult->anomaly_indicators);
    }

    #[Test]
    public function it_flags_grade_inflation_as_tampered_at_99_percent()
    {
        // Declared 1.75 (better) but transcript shows 2.50 — diff = 0.75 > 0.05, declared > extracted
        [$application, $document] = $this->buildScenario('1.75', 2.50, 12.50);

        (new ScanDocumentJob($application->id))->handle();

        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);
        $this->assertEquals(99.00, $aiResult->fraud_probability);
        $this->assertEquals('Tampered (Grade Discrepancy)', $aiResult->classification);
        $this->assertContains('gwa_discrepancy:declared_1.75_vs_extracted_2.5', $aiResult->anomaly_indicators);
    }

    #[Test]
    public function it_flags_inverse_variance_as_review_needed_not_tampered()
    {
        // Student declared 2.50 (worse) but transcript shows 1.75 (better) — diff = 0.75 but declared < extracted
        // This is a data-entry mistake, not fraud.
        [$application, $document] = $this->buildScenario('2.50', 1.75, 12.50);

        (new ScanDocumentJob($application->id))->handle();

        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);

        // Should be 45% minimum, NOT 99%
        $this->assertGreaterThanOrEqual(45.0, $aiResult->fraud_probability);
        $this->assertNotEquals(99.00, $aiResult->fraud_probability);
        $this->assertEquals('Review Needed (Grade Input Variance)', $aiResult->classification);
        $this->assertContains('gwa_input_variance:declared_2.5_vs_extracted_1.75', $aiResult->anomaly_indicators);
    }
}
