<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\Document;
use App\Models\ApplicationField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentAccessAndResubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $student;
    protected Scholarship $scholarship;
    protected AcademicTerm $term;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->term = AcademicTerm::create([
            'academic_year' => '2026-2027',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'University Excellence Grant',
            'min_gwa_required' => 1.75,
            'status' => 'Active',
            'max_renewals' => 4,
        ]);

        // Staff evaluator with unconstrained scholarship assignments
        $this->staff = User::create([
            'name' => 'OSA Evaluator Staff',
            'email' => 'staff_eval@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Student Applicant',
            'email' => 'scholar_student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);
    }

    public function test_staff_evaluator_can_view_student_document_without_403(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Pending',
            'is_archived' => false,
        ]);

        $filePath = 'uploads/test_cog.pdf';
        Storage::disk('local')->put($filePath, '%PDF-1.4 Fake PDF Content');

        $document = Document::create([
            'application_id' => $app->id,
            'document_type' => 'COG',
            'file_path' => $filePath,
            'original_name' => 'student_cog.pdf',
            'uploaded_by' => $this->student->id,
            'is_synced' => true,
        ]);

        // Staff views document image/stream
        $response = $this->actingAs($this->staff)->get(route('document.view', $document->id));
        $response->assertStatus(200);
    }

    public function test_staff_evaluator_can_view_custom_field_file_without_403(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Pending',
            'is_archived' => false,
        ]);

        $filePath = 'uploads/indigency_certificate.pdf';
        Storage::disk('local')->put($filePath, '%PDF-1.4 Fake Indigency');

        $field = ApplicationField::create([
            'application_id' => $app->id,
            'field_name' => 'Certificate of Indigency',
            'field_value' => $filePath,
            'is_synced' => true,
        ]);

        $response = $this->actingAs($this->staff)->get(route('application-field.file', $field->id));
        $response->assertStatus(200);
    }

    public function test_resubmission_keeps_same_application_number_and_updates_only_targeted_file(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Returned',
            'remarks' => 'Please provide a clearer Certificate of Grades.',
            'is_archived' => false,
        ]);

        $initialAppId = $app->id;

        // Existing COG
        $initialCog = Document::create([
            'application_id' => $app->id,
            'document_type' => 'COG',
            'file_path' => 'uploads/blurry_cog.pdf',
            'original_name' => 'blurry_cog.pdf',
            'uploaded_by' => $this->student->id,
            'is_synced' => true,
        ]);

        // Existing other document (e.g. Indigency) that should NOT be modified
        $otherDoc = Document::create([
            'application_id' => $app->id,
            'document_type' => 'Certificate of Indigency',
            'file_path' => 'uploads/valid_indigency.pdf',
            'original_name' => 'valid_indigency.pdf',
            'uploaded_by' => $this->student->id,
            'is_synced' => true,
        ]);

        $newFile = UploadedFile::fake()->create('clear_cog.pdf', 500, 'application/pdf');

        // Student resubmits corrected COG
        $response = $this->actingAs($this->student)->post(route('student.application.reupload', $app->id), [
            'document_type' => 'COG',
            'file' => $newFile,
        ]);

        $response->assertStatus(302);

        $app->refresh();

        // 1. Application number MUST be the exact same
        $this->assertEquals($initialAppId, $app->id);

        // 2. Status transitioned back to Pending
        $this->assertEquals('Pending', $app->status);
        $this->assertFalse($app->is_archived);

        // 3. COG was updated to the new file
        $updatedCog = Document::where('application_id', $app->id)->where('document_type', 'COG')->first();
        $this->assertNotNull($updatedCog);
        $this->assertEquals('clear_cog.pdf', $updatedCog->original_name);
        $this->assertEquals('replaced', $updatedCog->upload_event);

        // 4. The other document (Certificate of Indigency) was NOT touched
        $otherDoc->refresh();
        $this->assertEquals('uploads/valid_indigency.pdf', $otherDoc->file_path);
        $this->assertEquals('valid_indigency.pdf', $otherDoc->original_name);
    }

    public function test_resubmission_of_rejected_application_unarchives_and_preserves_application_number(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Rejected',
            'remarks' => 'Ineligible format. Please re-upload official copy.',
            'is_archived' => true,
        ]);

        $initialAppId = $app->id;

        $newFile = UploadedFile::fake()->create('official_transcript.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->student)->post(route('student.application.reupload', $app->id), [
            'document_type' => 'COG',
            'file' => $newFile,
        ]);

        $response->assertStatus(302);
        $app->refresh();

        $this->assertEquals($initialAppId, $app->id);
        $this->assertEquals('Pending', $app->status);
        $this->assertFalse($app->is_archived);
    }
}
