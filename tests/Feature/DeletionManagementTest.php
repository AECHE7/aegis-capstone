<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Document;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeletionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $admin;
    private User $superadmin;
    private Scholarship $scholarship;
    private AcademicTerm $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicTerm = AcademicTerm::create([
            'academic_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'DOST Merit',
            'description' => 'Test Merit Scholarship',
            'min_gwa_required' => 1.75,
            'status' => 'Active'
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'is_active' => true
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true
        ]);
        // Assign scholarship to admin
        $this->admin->scholarships()->attach($this->scholarship->id);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'email_verified_at' => now(),
            'is_active' => true
        ]);
    }

    public function test_student_can_cancel_pending_application_soft_delete()
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson(route('student.application.cancel', $application->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert it is soft-deleted
        $this->assertSoftDeleted('applications', ['id' => $application->id]);
        
        // Assert status log is created
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $application->id,
            'status' => 'Cancelled',
            'changed_by' => $this->student->id
        ]);
    }

    public function test_student_can_restore_cancelled_application()
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);
        $application->delete(); // Soft delete

        $response = $this->actingAs($this->student)
            ->postJson(route('student.application.restore', $application->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert restored
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'deleted_at' => null
        ]);
    }

    public function test_student_can_withdraw_pending_application_permanently()
    {
        Storage::fake('local');

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);

        $doc = Document::create([
            'application_id' => $application->id,
            'original_name' => 'cog.pdf',
            'file_path' => 'documents/cog.pdf'
        ]);

        Storage::disk('local')->put('documents/cog.pdf', 'dummy content');
        
        $application->delete(); // Soft delete first

        $response = $this->actingAs($this->student)
            ->postJson(route('student.application.withdraw', $application->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert database record is gone
        $this->assertDatabaseMissing('applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing('documents/cog.pdf');
    }

    public function test_student_cannot_cancel_approved_application()
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Approved'
        ]);

        $response = $this->actingAs($this->student)
            ->postJson(route('student.application.cancel', $application->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'deleted_at' => null]);
    }

    public function test_admin_can_view_and_restore_soft_deleted_application()
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);
        $application->delete();

        // 1. Verify index returns soft-deleted app when status=Cancelled is passed
        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.dashboard', ['status' => 'Cancelled']));

        $response->assertStatus(200);
        $this->assertStringContainsString('APP-' . $application->id, $response->json('html'));

        // 2. Verify admin can restore
        $response2 = $this->actingAs($this->admin)
            ->postJson(route('admin.restore', $application->id));

        $response2->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'deleted_at' => null
        ]);
    }

    public function test_superadmin_can_manage_trash_and_permanently_delete()
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);
        $application->delete();

        // View Trash Dashboard
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.trash'));
        $response->assertStatus(200)
            ->assertSee('APP-' . $application->id);

        // Force delete
        $response2 = $this->actingAs($this->superadmin)
            ->deleteJson(route('superadmin.applications.force-delete', $application->id));

        $response2->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('applications', ['id' => $application->id]);
    }

    public function test_superadmin_can_soft_delete_and_restore_scholarship()
    {
        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('superadmin.scholarships.delete', $this->scholarship->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('scholarships', ['id' => $this->scholarship->id]);

        // Restore scholarship
        $response2 = $this->actingAs($this->superadmin)
            ->postJson(route('superadmin.scholarships.restore', $this->scholarship->id));

        $response2->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('scholarships', [
            'id' => $this->scholarship->id,
            'deleted_at' => null
        ]);
    }

    public function test_superadmin_cannot_force_delete_scholarship_with_applications()
    {
        // Create application
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending'
        ]);

        $this->scholarship->delete(); // Soft delete first

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('superadmin.scholarships.force-delete', $this->scholarship->id));

        $response->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertSoftDeleted('scholarships', ['id' => $this->scholarship->id]);
    }

    public function test_superadmin_can_soft_delete_and_permanently_delete_staff()
    {
        $staff = User::factory()->create([
            'role' => 'admin',
            'is_active' => true
        ]);

        // Soft delete
        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('superadmin.staff.delete', $staff->id));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('users', ['id' => $staff->id]);

        // Force delete
        $response2 = $this->actingAs($this->superadmin)
            ->deleteJson(route('superadmin.staff.force-delete', $staff->id));

        $response2->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }
}
