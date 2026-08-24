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

    #[Test]
    public function it_flags_applications_with_high_fraud_probability_on_gwa_discrepancy()
    {
        Storage::fake('local');

        // Create student & scholarship
        $student = User::create([
            'name' => 'Student Test',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        // Student claims 1.75 GWA in application
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        // Create document
        $filePath = 'uploads/test_cog.pdf';
        Storage::disk('local')->put($filePath, 'fake pdf content');

        $document = Document::create([
            'application_id' => $application->id,
            'file_path' => $filePath,
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG'
        ]);

        // Mock the AI service to return an extracted GWA of 2.50 (meaning a grade discrepancy exists!)
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'fraud_probability' => 12.50,
                'classification' => 'Authentic',
                'extracted_gwa' => 2.50, // Mismatch with 1.75!
                'paths' => [
                    'heatmap_path' => 'heatmap_outputs/test_heatmap.jpg',
                    'ela_path' => 'ela_outputs/test_ela.jpg'
                ]
            ], 200)
        ]);

        // Run the scan job
        (new ScanDocumentJob($application->id))->handle();

        // Assert that the document was flagged due to the grade discrepancy
        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);
        $this->assertEquals(99.00, $aiResult->fraud_probability);
        $this->assertEquals('Tampered (Grade Discrepancy)', $aiResult->classification);
    }

    #[Test]
    public function it_keeps_original_ai_prediction_when_gwa_matches()
    {
        Storage::fake('local');

        $student = User::create([
            'name' => 'Student Test',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        // Student claims 1.75 GWA in application
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        $filePath = 'uploads/test_cog.pdf';
        Storage::disk('local')->put($filePath, 'fake pdf content');

        $document = Document::create([
            'application_id' => $application->id,
            'file_path' => $filePath,
            'original_name' => 'test_cog.pdf',
            'document_type' => 'COG'
        ]);

        // Mock the AI service to return an extracted GWA of 1.75 (matches!)
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'fraud_probability' => 12.50,
                'classification' => 'Authentic',
                'extracted_gwa' => 1.75, // Matches!
                'paths' => [
                    'heatmap_path' => 'heatmap_outputs/test_heatmap.jpg',
                    'ela_path' => 'ela_outputs/test_ela.jpg'
                ]
            ], 200)
        ]);

        (new ScanDocumentJob($application->id))->handle();

        // Assert that the original prediction was kept
        $aiResult = AIResult::where('document_id', $document->id)->first();
        $this->assertNotNull($aiResult);
        $this->assertEquals(12.50, $aiResult->fraud_probability);
        $this->assertEquals('Authentic', $aiResult->classification);
    }
}
