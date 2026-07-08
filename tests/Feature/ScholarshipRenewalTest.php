<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\Scholarship;

class ScholarshipRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_load_apply_form_with_renewal_parameter()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $prevTerm = AcademicTerm::create([
            'semester' => '2nd',
            'academic_year' => '2025-2026',
            'is_active' => false,
        ]);

        $activeTerm = AcademicTerm::create([
            'semester' => '1st',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        $scholarship = Scholarship::create([
            'name' => 'GAD Scholarship',
            'min_gwa_required' => 2.0,
            'status' => 'Active',
        ]);

        $prevApp = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'academic_term_id' => $prevTerm->id,
            'gwa' => 1.75,
            'status' => 'Approved',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.apply', ['renew_from' => $prevApp->id]));

        $response->assertStatus(200);
        $response->assertViewHas('prevApp');
        $this->assertEquals($prevApp->id, $response->viewData('prevApp')->id);
    }

    public function test_can_submit_renewal_application()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $prevTerm = AcademicTerm::create([
            'semester' => '2nd',
            'academic_year' => '2025-2026',
            'is_active' => false,
        ]);

        $activeTerm = AcademicTerm::create([
            'semester' => '1st',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        $scholarship = Scholarship::create([
            'name' => 'GAD Scholarship',
            'min_gwa_required' => 2.0,
            'status' => 'Active',
        ]);

        $prevApp = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'academic_term_id' => $prevTerm->id,
            'gwa' => 1.75,
            'status' => 'Approved',
        ]);

        // Submit the renewal form POST request
        \Illuminate\Support\Facades\Cache::forget('active_academic_term');
        \Illuminate\Support\Facades\Cache::forget('active_scholarships_list');
        $response = $this->actingAs($student)
            ->post(route('student.store'), [
                'scholarship_id' => $scholarship->id,
                'program_name' => $scholarship->name,
                'gwa' => 1.85,
                'is_renewal' => '1',
                'previous_application_id' => $prevApp->id,
            ]);

        $response->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('applications', [
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'gwa' => 1.85,
            'is_renewal' => true,
            'previous_application_id' => $prevApp->id,
        ]);
    }
}
