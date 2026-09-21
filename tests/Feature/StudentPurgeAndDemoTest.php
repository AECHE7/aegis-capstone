<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Services\StudentPurgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StudentPurgeAndDemoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@clsu.edu.ph',
            'is_active' => true,
        ]);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'director@clsu.edu.ph',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function student_purge_service_removes_students_and_preserves_staff()
    {
        // Create student users with applications
        $student1 = User::factory()->create(['role' => 'student', 'email' => 'student1@clsu2.edu.ph']);
        $student2 = User::factory()->create(['role' => 'student', 'email' => 'student2@clsu2.edu.ph']);

        $scholarship = Scholarship::create([
            'name' => 'DOST Scholar',
            'min_gwa_required' => 2.0,
            'status' => 'Active',
        ]);
        $term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        Application::create([
            'user_id' => $student1->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $term->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending',
        ]);

        $this->assertEquals(2, User::where('role', 'student')->count());
        $this->assertEquals(1, Application::count());

        $purgedCount = StudentPurgeService::purgeAllStudents();

        $this->assertEquals(2, $purgedCount);
        $this->assertEquals(0, User::where('role', 'student')->count());
        $this->assertEquals(0, Application::count());

        // Ensure staff and admin were not touched
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
        $this->assertDatabaseHas('users', ['id' => $this->superadmin->id]);
    }

    #[Test]
    public function purge_artisan_command_executes_successfully()
    {
        User::factory()->count(3)->create(['role' => 'student']);
        $this->assertEquals(3, User::where('role', 'student')->count());

        $this->artisan('aegis:purge-students --force')
            ->assertExitCode(0);

        $this->assertEquals(0, User::where('role', 'student')->count());
    }

    #[Test]
    public function superadmin_settings_displays_demo_hub_and_purge_feature_is_safely_removed()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.settings'));

        $response->assertStatus(200);
        $response->assertSee('Interactive Training & Director Walkthrough', false);
        $response->assertSee('Launch Director Guide', false);
        // Assert purge feature is completely removed from UI for security
        $response->assertDontSee('Testing & Database Maintenance', false);
        $response->assertDontSee('Purge All Student User Accounts', false);
        $response->assertDontSee('confirmPurgeStudents', false);
        $response->assertSee('openSystemTourModal', false);
    }

    #[Test]
    public function account_settings_displays_interactive_guided_demo_card()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('profile.security'));

        $response->assertStatus(200);
        $response->assertSee('Interactive Guided Demo & Training Guide', false);
        $response->assertSee('Start Guided Tour', false);
        $response->assertSee('openSystemTourModal', false);
    }

    #[Test]
    public function system_demo_modal_renders_interactive_role_worksheets_and_tabs()
    {
        // 1. Student view renders modal with all three role tabs, student tab active
        $student = User::factory()->create(['role' => 'student']);
        $studentResp = $this->actingAs($student)->get(route('profile.security'));
        $studentResp->assertStatus(200);
        $studentResp->assertSee('id="systemDemoModal"', false);
        $studentResp->assertSee('id="student-demo-tab"', false);
        $studentResp->assertSee('id="admin-demo-tab"', false);
        $studentResp->assertSee('id="director-demo-tab"', false);
        $studentResp->assertSee('Student Applicant', false);
        $studentResp->assertSee('OSA Staff Evaluator', false);
        $studentResp->assertSee('OSA Director / Super Admin', false);
        $studentResp->assertSee('STAGE 01', false);
        $studentResp->assertSee('Institutional Registration', false);

        // 2. Admin view renders modal with tabs and evaluator steps
        $adminResp = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminResp->assertStatus(200);
        $adminResp->assertSee('id="systemDemoModal"', false);
        $adminResp->assertSee('id="student-demo-tab"', false);
        $adminResp->assertSee('id="admin-demo-tab"', false);
        $adminResp->assertSee('id="director-demo-tab"', false);
        $adminResp->assertSee('Live Review Queue', false);

        // 3. Superadmin view renders modal with tabs and director modules
        $superadminResp = $this->actingAs($this->superadmin)->get(route('superadmin.settings'));
        $superadminResp->assertStatus(200);
        $superadminResp->assertSee('id="systemDemoModal"', false);
        $superadminResp->assertSee('id="student-demo-tab"', false);
        $superadminResp->assertSee('id="admin-demo-tab"', false);
        $superadminResp->assertSee('id="director-demo-tab"', false);
        $superadminResp->assertSee('Executive Analytics', false);
    }

    #[Test]
    public function user_can_reset_interactive_tour()
    {
        $user = User::factory()->create([
            'role' => 'student',
            'has_completed_tour' => true,
        ]);

        $response = $this->actingAs($user)->post(route('tour.reset'));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->has_completed_tour);
    }
}
