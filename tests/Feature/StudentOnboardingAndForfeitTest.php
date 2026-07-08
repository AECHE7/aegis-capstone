<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\Scholarship;

class StudentOnboardingAndForfeitTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_complete_onboarding_tour()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'has_completed_tour' => false,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('student.complete-tour'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertTrue($student->fresh()->has_completed_tour);
    }

    public function test_student_can_forfeit_approved_scholarship()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $term = AcademicTerm::create([
            'semester' => '1st',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        $scholarship = Scholarship::create([
            'name' => 'GAD Scholarship',
            'min_gwa_required' => 2.0,
            'status' => 'Active',
        ]);

        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'academic_term_id' => $term->id,
            'gwa' => 1.75,
            'status' => 'Approved',
        ]);

        $response = $this->actingAs($student)
            ->post(route('student.application.forfeit', $application->id), [
                'reason' => 'I got another scholarship offer with higher stipend.',
            ]);

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('success');

        $freshApplication = $application->fresh();
        $this->assertEquals('Cancelled', $freshApplication->status);
        $this->assertEquals('I got another scholarship offer with higher stipend.', $freshApplication->forfeit_reason);

        // Assert status log is created
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $application->id,
            'status' => 'Cancelled',
            'remarks' => 'Scholarship was forfeited/backed-out by the scholar. Reason: I got another scholarship offer with higher stipend.',
        ]);
    }
}
