<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\StudentProfile;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\ApplicationField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovedApplicationPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $admin;
    protected Scholarship $scholarship;
    protected AcademicTerm $term;
    protected Application $approvedApp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'CLSU Alumni Centennial Leadership Grant',
            'description' => 'Official leadership grant for deserving students.',
            'min_gwa_required' => 1.75,
            'status' => 'active',
            'max_renewals' => 4,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Dela Cruz, Juan M.',
            'email' => 'juan.delacruz@clsu.edu.ph',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '21-1234',
            'college' => 'College of Engineering',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '09171234567',
            'guardian_name' => 'Pedro Dela Cruz',
            'emergency_contact_number' => '09187654321',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'OSA Staff Evaluator',
            'email' => 'staff.evaluator@clsu.edu.ph',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $this->scholarship->staff()->attach($this->admin->id);

        $this->approvedApp = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => 'CLSU Alumni Centennial Leadership Grant',
            'gwa' => 1.45,
            'status' => 'Approved',
            'remarks' => 'Applicant certified with honors and verified COG.',
            'evaluated_by' => $this->admin->id,
            'is_renewal' => false,
        ]);

        $doc = Document::create([
            'application_id' => $this->approvedApp->id,
            'document_type' => 'COG',
            'file_path' => 'uploads/test_cog.png',
            'original_name' => 'official_transcript.png',
            'uploaded_by' => $this->student->id,
        ]);

        AIResult::create([
            'document_id' => $doc->id,
            'fraud_probability' => 2.50,
            'classification' => 'authentic',
        ]);

        ApplicationField::create([
            'application_id' => $this->approvedApp->id,
            'field_name' => 'Annual Household Income',
            'field_value' => 'PHP 180,000.00',
        ]);
    }

    #[Test]
    public function clsu_and_osa_seal_assets_exist_on_disk(): void
    {
        $this->assertFileExists(public_path('images/clsu-seal.png'));
        $this->assertFileExists(public_path('images/osa-seal.png'));
    }

    #[Test]
    public function dynamic_application_pdf_renders_without_errors(): void
    {
        $app = Application::with([
            'user.profile',
            'scholarship',
            'academicTerm',
            'customFields',
            'document.aiResult',
            'documents',
            'evaluator'
        ])->find($this->approvedApp->id);

        $pdf = Pdf::loadView('emails.application_form_pdf', ['application' => $app]);
        $content = $pdf->output();

        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF', $content);
    }

    #[Test]
    public function student_can_download_own_approved_form(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.application.download-form', $this->approvedApp->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function student_cannot_download_unapproved_form(): void
    {
        $pendingApp = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => 'CLSU Alumni Centennial Leadership Grant',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.application.download-form', $pendingApp->id));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function admin_can_download_application_evaluation_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.application.download-form', $this->approvedApp->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
