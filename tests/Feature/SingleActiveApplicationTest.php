<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\Application;

class SingleActiveApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $scholarship;
    protected $activeTerm;
    protected $previousTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        
        $this->scholarship = Scholarship::create([
            'name' => 'DOST Scholar',
            'min_gwa_required' => 2.0,
            'status' => 'Active'
        ]);

        $this->activeTerm = AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => true
        ]);

        $this->previousTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => false
        ]);
    }

    /** @test */
    public function student_with_no_application_can_access_apply_form()
    {
        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertStatus(200);
    }

    /** @test */
    public function student_with_pending_application_is_blocked()
    {
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->activeTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error', 'Action Denied: You already have an active application or scholarship for this academic term.');

        // Test POST route too
        $postResponse = $this->actingAs($this->student)
                             ->post(route('student.store'), [
                                 'scholarship_id' => $this->scholarship->id,
                                 'gwa' => '1.75'
                             ]);

        $postResponse->assertSessionHasErrors('duplicate');
    }

    /** @test */
    public function student_with_under_review_application_is_blocked()
    {
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->activeTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Under Review'
        ]);

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function student_with_approved_application_in_active_term_is_blocked()
    {
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->activeTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Approved'
        ]);

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function student_with_approved_application_in_previous_term_is_allowed()
    {
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->previousTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Approved'
        ]);

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertStatus(200);
    }

    /** @test */
    public function student_with_rejected_application_is_allowed()
    {
        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->activeTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Rejected'
        ]);

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertStatus(200);
    }

    /** @test */
    public function student_with_cancelled_application_is_allowed()
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->activeTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        // Soft delete the application (cancel it)
        $app->delete();

        $response = $this->actingAs($this->student)
                         ->get(route('student.apply'));

        $response->assertStatus(200);
    }
}
