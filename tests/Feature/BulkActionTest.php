<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $student;
    private Scholarship $scholarshipA;
    private Scholarship $scholarshipB;
    private AcademicTerm $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicTerm = AcademicTerm::create([
            'academic_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->scholarshipA = Scholarship::create([
            'name' => 'Scholarship A',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $this->scholarshipB = Scholarship::create([
            'name' => 'Scholarship B',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        // Assign Admin to Scholarship A only
        $this->admin->scholarships()->attach($this->scholarshipA->id);
    }

    public function test_admin_can_bulk_approve_assigned_scholarship_applications(): void
    {
        $app1 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipA->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipA->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $app2 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipA->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipA->name,
            'gwa' => 1.50,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.applications.bulk-action'), [
            'application_ids' => [$app1->id, $app2->id],
            'status' => 'Approved',
            'remarks' => 'Bulk approved in test',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $app1->refresh();
        $app2->refresh();

        $this->assertEquals('Approved', $app1->status);
        $this->assertEquals('Approved', $app2->status);
    }

    public function test_admin_cannot_bulk_process_unassigned_scholarship_applications(): void
    {
        // Application for unassigned Scholarship B
        $appB = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipB->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipB->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        // Application for assigned Scholarship A
        $appA = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipA->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipA->name,
            'gwa' => 1.50,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.applications.bulk-action'), [
            'application_ids' => [$appA->id, $appB->id],
            'status' => 'Approved',
            'remarks' => 'Attempt unassigned bulk process',
        ]);

        $response->assertStatus(200); // Route completes, but skips unauthorized ones

        $appA->refresh();
        $appB->refresh();

        // App A should be approved
        $this->assertEquals('Approved', $appA->status);
        // App B should remain Pending (skipped due to 403 authorization block)
        $this->assertEquals('Pending', $appB->status);
    }

    public function test_non_admin_cannot_bulk_process_applications(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipA->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipA->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->student)->postJson(route('admin.applications.bulk-action'), [
            'application_ids' => [$app->id],
            'status' => 'Approved',
        ]);

        $response->assertStatus(403); // Forbidden for student
    }

    public function test_admin_can_save_staff_notes(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarshipA->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarshipA->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->admin)->patchJson(route('admin.applications.save-notes', $app->id), [
            'admin_notes' => 'Some private staff feedback notes here.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $app->refresh();
        $this->assertEquals('Some private staff feedback notes here.', $app->admin_notes);
    }

    public function test_student_blocked_when_exceeding_max_renewals(): void
    {
        $this->scholarshipA->update(['max_renewals' => 2]);

        for ($i = 0; $i < 2; $i++) {
            $inactiveTerm = AcademicTerm::create([
                'academic_year' => '2024-2025',
                'semester' => ($i + 1) . 'nd Semester',
                'is_active' => false,
            ]);

            Application::create([
                'user_id' => $this->student->id,
                'scholarship_id' => $this->scholarshipA->id,
                'academic_term_id' => $inactiveTerm->id,
                'program_name' => $this->scholarshipA->name,
                'gwa' => 1.50,
                'status' => 'Approved',
            ]);
        }

        $response = $this->actingAs($this->student)->postJson(route('student.store'), [
            'scholarship_id' => $this->scholarshipA->id,
            'gwa' => 1.50,
            'document' => \Illuminate\Http\UploadedFile::fake()->create('cog.png', 500)
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Application Blocked: You have reached the maximum renewal limit (2) for the ' . $this->scholarshipA->name . '.'
        ]);
    }
}
