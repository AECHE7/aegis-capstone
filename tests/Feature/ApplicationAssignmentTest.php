<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\ApplicationAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $staff1;
    private User $staff2;
    private Scholarship $scholarship;
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

        $this->staff1 = User::factory()->create([
            'name' => 'Staff 1',
            'email' => 'staff1@clsu.edu.ph',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->staff2 = User::factory()->create([
            'name' => 'Staff 2',
            'email' => 'staff2@clsu.edu.ph',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Test Scholarship',
            'description' => 'A test scholarship program',
            'min_gwa_required' => 2.00,
            'max_renewals' => 4,
            'status' => 'Active',
        ]);
    }

    /** @test */
    public function it_assigns_submitted_application_to_staff_with_lowest_workload()
    {
        // Link both staff to the scholarship program
        $this->staff1->scholarships()->attach($this->scholarship->id);
        $this->staff2->scholarships()->attach($this->scholarship->id);

        // Pre-create an application and assign it to staff1
        $app1 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
            'assigned_to' => $this->staff1->id,
        ]);

        // Create a second student to submit the second application
        $student2 = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Submit a new application through the student route
        $this->actingAs($student2);
        $response = $this->post(route('student.store'), [
            'scholarship_id' => $this->scholarship->id,
            'gwa' => 1.75,
        ]);

        $response->assertStatus(302);

        // Verify the second application gets assigned to staff2 (workload: 0 vs staff1: 1)
        $app2 = Application::where('id', '!=', $app1->id)->latest()->first();
        $this->assertNotNull($app2);
        $this->assertEquals($this->staff2->id, $app2->assigned_to);
    }

    /** @test */
    public function it_falls_back_to_any_active_staff_if_none_assigned_to_program()
    {
        // Neither staff is explicitly linked to the scholarship

        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $assigned = ApplicationAssignmentService::assign($app);

        // Should successfully assign to one of the active staff
        $this->assertNotNull($assigned);
        $this->assertTrue(in_array($assigned->id, [$this->staff1->id, $this->staff2->id], true));
        $this->assertEquals($assigned->id, $app->fresh()->assigned_to);
    }

    /** @test */
    public function it_reassigns_pending_applications_when_staff_is_deactivated()
    {
        // Link both staff to the scholarship
        $this->staff1->scholarships()->attach($this->scholarship->id);
        $this->staff2->scholarships()->attach($this->scholarship->id);

        // Create application assigned to staff1
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
            'assigned_to' => $this->staff1->id,
        ]);

        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        // Superadmin deactivates staff1
        $this->actingAs($superadmin);
        $response = $this->post(route('superadmin.staff.revoke', $this->staff1->id));
        $response->assertStatus(302);

        // Verify the application gets reassigned to staff2
        $this->assertEquals($this->staff2->id, $app->fresh()->assigned_to);
        $this->assertFalse($this->staff1->fresh()->is_active);
    }
}
