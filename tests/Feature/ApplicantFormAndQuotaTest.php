<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\StudentProfile;
use App\Models\ApplicationField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ApplicantFormAndQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $unassignedAdmin;
    protected User $student;
    protected AcademicTerm $term;
    protected Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $this->superadmin = User::factory()->create([
            'name' => 'Dr. Director SuperAdmin',
            'email' => 'director@clsu.edu.ph',
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'name' => 'OSA Staff Evaluator',
            'email' => 'staff@clsu.edu.ph',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->unassignedAdmin = User::factory()->create([
            'name' => 'Other Staff Evaluator',
            'email' => 'otherstaff@clsu.edu.ph',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->student = User::factory()->create([
            'name' => 'Santos, Maria Clara',
            'email' => 'maria.santos@clsu.edu.ph',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '22-54321',
            'college' => 'College of Science',
            'course' => 'BS Chemistry',
            'year_level' => '2nd Year',
            'contact_number' => '09187654321',
            'guardian_name' => 'Capitan Tiago Santos',
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'DOST-SEI Merit Scholarship',
            'description' => 'Science and Technology scholarship grant with quotas.',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
            'max_renewals' => 4,
            'quota' => 2,
        ]);

        // Assign staff to scholarship
        $this->scholarship->staff()->sync([$this->admin->id]);
    }

    #[Test]
    public function superadmin_can_create_scholarship_with_quota(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.scholarships.store'), [
                'name' => 'CLSU Agri-Tech Innovation Grant',
                'description' => 'Grant for agriculture and biosystems students.',
                'max_renewals' => 4,
                'quota' => 50,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('scholarships', [
            'name' => 'CLSU Agri-Tech Innovation Grant',
            'quota' => 50,
        ]);
    }

    #[Test]
    public function superadmin_can_update_scholarship_quota(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->put(route('superadmin.scholarships.update', $this->scholarship->id), [
                'name' => 'DOST-SEI Merit Scholarship Updated',
                'description' => 'Updated grant description.',
                'max_renewals' => 6,
                'quota' => 5,
            ]);

        $response->assertSessionHas('success');
        $this->scholarship->refresh();
        $this->assertEquals(5, $this->scholarship->quota);
    }

    #[Test]
    public function scholarship_quota_helper_methods_and_waitlist_indicator(): void
    {
        // Initially 0 approved
        $this->assertEquals(0, $this->scholarship->approvedCount());
        $this->assertEquals(2, $this->scholarship->availableSlots());
        $this->assertFalse($this->scholarship->isQuotaExhausted());
        $this->assertEquals(0.0, $this->scholarship->quotaUtilizationPct());

        // Create 2 approved applications
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Approved',
            'gwa' => 1.50,
        ]);

        $student2 = User::factory()->create(['role' => 'student']);
        Application::create([
            'user_id' => $student2->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Approved',
            'gwa' => 1.75,
        ]);

        $this->assertEquals(2, $this->scholarship->approvedCount());
        $this->assertEquals(0, $this->scholarship->availableSlots());
        $this->assertTrue($this->scholarship->isQuotaExhausted());
        $this->assertEquals(100.0, $this->scholarship->quotaUtilizationPct());
    }

    #[Test]
    public function staff_and_superadmin_can_preview_form_via_ajax(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Under Review',
            'gwa' => 1.65,
        ]);

        ApplicationField::create([
            'application_id' => $app->id,
            'field_name' => 'annual_income',
            'field_value' => 'PHP 180,000.00',
        ]);

        // 1. Staff preview
        $responseStaff = $this->actingAs($this->admin)
            ->getJson(route('admin.applications.preview-form', $app->id));

        $responseStaff->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'Under Review')
            ->assertJsonStructure(['success', 'application_id', 'control_no', 'student_name', 'program_name', 'status', 'download_url', 'html']);

        $this->assertStringContainsString('Santos, Maria Clara', $responseStaff->json('html'));
        $this->assertStringContainsString('Annual Income', $responseStaff->json('html'));
        $this->assertStringContainsString('PHP 180,000.00', $responseStaff->json('html'));

        // 2. Superadmin preview
        $responseSuperAdmin = $this->actingAs($this->superadmin)
            ->getJson(route('admin.applications.preview-form', $app->id));

        $responseSuperAdmin->assertOk()
            ->assertJsonPath('success', true);
    }

    #[Test]
    public function staff_and_superadmin_can_download_form_pdf_for_any_status(): void
    {
        // Testing a 'Pending' application
        $pendingApp = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Pending',
            'gwa' => 1.85,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.application.download-form', $pendingApp->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString("APP-{$pendingApp->id}_Official_Evaluation_Form.pdf", $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function unassigned_staff_cannot_preview_or_download_form(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Under Review',
        ]);

        $previewResponse = $this->actingAs($this->unassignedAdmin)
            ->getJson(route('admin.applications.preview-form', $app->id));
        $previewResponse->assertForbidden();

        $downloadResponse = $this->actingAs($this->unassignedAdmin)
            ->get(route('admin.application.download-form', $app->id));
        $downloadResponse->assertForbidden();
    }

    #[Test]
    public function staff_and_director_can_access_applicant_forms_module(): void
    {
        // 1. Staff access
        $staffResponse = $this->actingAs($this->admin)
            ->get(route('admin.applicant-forms.index'));
        $staffResponse->assertOk();
        $staffResponse->assertSee('Applicant & Scholar Information Forms', false);
        $staffResponse->assertSee('OFFICIAL FORM GENERATOR');

        // 2. Director (SuperAdmin) access
        $directorResponse = $this->actingAs($this->superadmin)
            ->get(route('admin.applicant-forms.index'));
        $directorResponse->assertOk();
        $directorResponse->assertSee('Applicant & Scholar Information Forms', false);

        // 3. Search and filter query parameter
        $filterResponse = $this->actingAs($this->superadmin)
            ->get(route('admin.applicant-forms.index', ['status' => 'Approved', 'q' => 'Maria']));
        $filterResponse->assertOk();

        // 4. Student cannot access module
        $studentResponse = $this->actingAs($this->student)
            ->get(route('admin.applicant-forms.index'));
        $studentResponse->assertForbidden();
    }
}
