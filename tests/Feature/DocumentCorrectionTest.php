<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\Document;
use App\Models\StatusLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ScanDocumentJob;

class DocumentCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private $student;
    private $admin;
    private $scholarship;
    private $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Juan Student',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Barangay Scholar',
            'description' => 'For barangay official descendants',
            'min_gwa_required' => 2.50,
            'status' => 'Active',
        ]);

        $this->academicTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        $this->admin->scholarships()->attach($this->scholarship->id);
    }

    public function test_admin_can_return_application_for_document_correction(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Under Review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.updateStatus', $application->id), [
            'status' => 'Returned',
            'remarks' => 'Please upload a clearer copy of your Certificate of Grades.',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Verify status is updated to Returned
        $application->refresh();
        $this->assertEquals('Returned', $application->status);
        $this->assertEquals('Please upload a clearer copy of your Certificate of Grades.', $application->remarks);

        // Verify status log entry is created
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $application->id,
            'status' => 'Returned',
            'remarks' => 'Please upload a clearer copy of your Certificate of Grades.',
            'changed_by' => $this->admin->id,
        ]);
    }

    public function test_student_sees_correction_remarks_and_can_reupload_corrected_document(): void
    {
        Storage::fake('local');
        Queue::fake();

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Returned',
            'remarks' => 'Upload clearer COG.',
        ]);

        // Create an initial document
        $document = Document::create([
            'application_id' => $application->id,
            'document_type' => 'COG',
            'original_name' => 'old_cog.jpg',
            'file_path' => 'documents/old_cog.jpg',
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);

        // Access dashboard, verify student sees re-upload form
        $response = $this->actingAs($this->student)->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Document Correction Required');
        $response->assertSee('Upload clearer COG.');

        // Re-upload document
        $fakeImg = UploadedFile::fake()->create('corrected_cog.jpg', 200, 'image/jpeg');

        $reuploadResponse = $this->post(route('student.application.reupload', $application->id), [
            'cog_file' => $fakeImg,
        ]);

        $reuploadResponse->assertRedirect(route('student.dashboard'));

        // Verify application status changed back to Pending
        $application->refresh();
        $this->assertEquals('Pending', $application->status);

        // Verify document was updated and event marked as replaced
        $document->refresh();
        $this->assertEquals('corrected_cog.jpg', $document->original_name);
        $this->assertEquals('replaced', $document->upload_event);
        $this->assertEquals($this->student->id, $document->uploaded_by);

        // Verify status log entry for Pending resubmission
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $application->id,
            'status' => 'Pending',
            'remarks' => 'Resubmitted corrected COG document.',
            'changed_by' => $this->student->id,
        ]);

        // Verify scan document job was dispatched
        Queue::assertPushed(ScanDocumentJob::class);
    }
}
