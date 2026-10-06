<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\ScholarshipField;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\Setting;
use App\Services\ApplicationAutoApprovalService;
use App\Notifications\ScholarshipSlotsOpenedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;

class AcademicStatusAndScholarshipEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $staff;
    protected User $student;
    protected AcademicTerm $term;
    protected Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'academic_year' => '2026-2027',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->superadmin = User::create([
            'name' => 'System Director',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->staff = User::create([
            'name' => 'Evaluation Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);

        $profile = $this->student->profile;
        if (!$profile) {
            $profile = new \App\Models\StudentProfile();
            $profile->user_id = $this->student->id;
        }
        $profile->clsu_id_number = '23-1234';
        $profile->college = 'College of Science';
        $profile->course = 'BS Information Technology';
        $profile->year_level = '3rd Year';
        $profile->academic_status = 'Regular';
        $profile->contact_number = '09123456789';
        $profile->guardian_name = 'Maria Dela Cruz';
        $profile->emergency_contact_number = '09987654321';
        $profile->address = 'Science City of Munoz, Nueva Ecija';
        $profile->save();

        $this->scholarship = Scholarship::create([
            'name' => 'CLSU Academic Grant',
            'description' => 'Official institutional academic grant for deserving scholars.',
            'min_gwa_required' => 1.75,
            'quota' => 50,
            'status' => 'Active',
            'max_renewals' => 4,
        ]);

        $this->scholarship->staff()->attach($this->staff->id);
    }

    #[Test]
    public function test_student_can_submit_application_with_academic_status(): void
    {
        $this->actingAs($this->student);

        $response = $this->post(route('student.store'), [
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'academic_status' => 'Irregular',
            'dpa_consent' => '1',
        ]);

        $response->assertSessionHasNoErrors();

        $app = Application::where('user_id', $this->student->id)->first();
        $this->assertNotNull($app);
        $this->assertEquals('Irregular', $app->academic_status);
    }

    #[Test]
    public function test_auto_approval_bypassed_when_student_has_irregular_or_dropped_status(): void
    {
        Setting::set('auto_approval_enabled', '1');
        Setting::set('auto_approval_min_confidence', '90.0');

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'academic_status' => 'Irregular',
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $application->id,
            'document_type' => 'Certificate of Grades',
            'file_path' => 'documents/test.pdf',
            'original_name' => 'test.pdf',
        ]);

        AIResult::create([
            'document_id' => $doc->id,
            'fraud_probability' => 5.0,
            'classification' => 'Authentic',
            'anomaly_indicators' => [],
        ]);

        // Auto approval MUST return false because academic status is Irregular
        $result = ApplicationAutoApprovalService::evaluate($application);
        $this->assertFalse($result);
        $this->assertEquals('Pending', $application->fresh()->status);
    }

    #[Test]
    public function test_auto_approval_bypassed_when_document_has_incomplete_or_dropped_grades(): void
    {
        Setting::set('auto_approval_enabled', '1');

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'academic_status' => 'Regular',
            'status' => 'Pending',
        ]);

        $doc = Document::create([
            'application_id' => $application->id,
            'document_type' => 'Certificate of Grades',
            'file_path' => 'documents/test.pdf',
            'original_name' => 'test.pdf',
        ]);

        AIResult::create([
            'document_id' => $doc->id,
            'fraud_probability' => 5.0,
            'classification' => 'Authentic',
            'anomaly_indicators' => ['manual_check_required:incomplete_or_dropped_grades'],
        ]);

        // Auto approval MUST return false due to incomplete/dropped grades flag
        $result = ApplicationAutoApprovalService::evaluate($application);
        $this->assertFalse($result);
        $this->assertEquals('Pending', $application->fresh()->status);
    }

    #[Test]
    public function test_export_approved_students_csv_contains_required_columns_and_form_responses(): void
    {
        // Add custom fields to scholarship
        $incomeField = ScholarshipField::create([
            'scholarship_id' => $this->scholarship->id,
            'field_name' => 'family_income',
            'field_label' => 'Monthly Family Income',
            'field_type' => 'text',
            'is_required' => true,
        ]);

        // Create approved application
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.45,
            'academic_status' => 'Regular',
            'status' => 'Approved',
        ]);

        // Store applicant response on custom form field
        $application->customFields()->create([
            'field_name' => 'Monthly Family Income',
            'field_value' => 'PHP 18,500.00',
        ]);

        $this->actingAs($this->staff);

        $response = $this->get(route('admin.export-approved', [
            'scholarship_id' => $this->scholarship->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Verify base headers: id, name, course, year level, contact number, academic status, gwa
        $this->assertStringContainsString('id', $content);
        $this->assertStringContainsString('name', $content);
        $this->assertStringContainsString('course', $content);
        $this->assertStringContainsString('year level', $content);
        $this->assertStringContainsString('contact number', $content);
        $this->assertStringContainsString('academic status', $content);
        $this->assertStringContainsString('gwa', $content);
        $this->assertStringContainsString('Monthly Family Income', $content);

        // Verify applicant data in the stream
        $this->assertStringContainsString('23-1234', $content);
        $this->assertStringContainsString('Juan Dela Cruz', $content);
        $this->assertStringContainsString('BS Information Technology', $content);
        $this->assertStringContainsString('3rd Year', $content);
        $this->assertStringContainsString('09123456789', $content);
        $this->assertStringContainsString('PHP 18,500.00', $content);
    }

    #[Test]
    public function test_scholarship_attachment_upload_and_download(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superadmin);

        $file = UploadedFile::fake()->create('guidelines_template.pdf', 200, 'application/pdf');

        $response = $this->post(route('superadmin.scholarships.store'), [
            'name' => 'DOST S&T Scholarship',
            'description' => 'National DOST scholarship program',
            'min_gwa_required' => 1.75,
            'max_renewals' => 4,
            'quota' => 20,
            'attachment_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();

        $newScholarship = Scholarship::where('name', 'DOST S&T Scholarship')->first();
        $this->assertNotNull($newScholarship);
        $this->assertNotNull($newScholarship->attachment_path);
        $this->assertEquals('guidelines_template.pdf', $newScholarship->attachment_name);

        Storage::disk('public')->assertExists($newScholarship->attachment_path);

        // Student downloads attachment
        $this->actingAs($this->student);
        $downloadRes = $this->get(route('scholarships.download-attachment', $newScholarship->id));
        $downloadRes->assertOk();
    }

    #[Test]
    public function test_scholarship_revocation_with_premade_remarks_workflow(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Approved',
        ]);

        $this->actingAs($this->staff);

        $premadeReason = "Found to have Incomplete (INC) or Dropped (DRP) academic units during evaluation.";

        $response = $this->post(route('admin.revoke', $application->id), [
            'reason' => $premadeReason,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Revoked', $application->fresh()->status);
        $this->assertEquals($premadeReason, $application->fresh()->remarks);
    }

    #[Test]
    public function test_superadmin_can_broadcast_open_slots_notification_to_students(): void
    {
        Notification::fake();

        $this->actingAs($this->superadmin);

        $response = $this->post(route('superadmin.scholarships.notify-slots', $this->scholarship->id));

        $response->assertSessionHasNoErrors();

        Notification::assertSentTo(
            $this->student,
            ScholarshipSlotsOpenedNotification::class,
            function ($notif) {
                $data = $notif->toArray($this->student);
                return str_contains($data['title'], 'Slots Open')
                    && str_contains($data['url'], 'scholarship_id=' . $this->scholarship->id);
            }
        );
    }
}
