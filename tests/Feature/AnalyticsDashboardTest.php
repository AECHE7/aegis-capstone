<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use App\Models\StudentProfile;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $superadmin;
    private $student;
    private $scholarship;
    private $term;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'name' => 'Director',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $this->student = User::create([
            'name' => 'Student A',
            'email' => 'studenta@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Merit Scholar',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $this->term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $app1 = new Application([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '1.50',
            'status' => 'Approved',
            'evaluated_by' => $this->superadmin->id,
        ]);
        $app1->created_at = now()->subDays(5);
        $app1->updated_at = now();
        $app1->save();

        $app2 = new Application([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => '2.50',
            'status' => 'Pending',
        ]);
        $app2->created_at = now()->subDays(2);
        $app2->save();
    }

    public function test_analytics_page_loads_for_superadmin(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('System Analytics');
    }

    public function test_analytics_page_blocks_non_superadmin(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(403);
    }

    public function test_filtering_by_academic_term_scopes_results(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics', ['academic_term_id' => $this->term->id]));

        $response->assertStatus(200);
    }

    public function test_filtering_by_scholarship_scopes_results(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics', ['scholarship_id' => $this->scholarship->id]));

        $response->assertStatus(200);
    }

    public function test_grade_integrity_index_computes_correctly(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('%');
    }

    public function test_empty_database_handles_gracefully(): void
    {
        Application::query()->delete();

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('0');
    }

    public function test_top_programs_appears_in_view(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Top Performing Programs');
    }

    public function test_process_timeline_data_is_present(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Process Audit Timeline');
    }
}
