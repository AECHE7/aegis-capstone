<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Scholarship;
use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplyNullableGwaTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Scholarship $gwaScholarship;
    private Scholarship $noGwaScholarship;
    private AcademicTerm $activeTerm;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->gwaScholarship = Scholarship::create([
            'name' => 'GWA Limited Scholarship',
            'min_gwa_required' => 2.00,
            'max_renewals' => 4,
            'status' => 'Active',
        ]);

        $this->noGwaScholarship = Scholarship::create([
            'name' => 'No GWA Scholarship',
            'min_gwa_required' => null, // Optional
            'max_renewals' => 4,
            'status' => 'Active',
        ]);

        $this->activeTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function student_submitting_to_scholarship_with_gwa_limit_requires_valid_gwa()
    {
        // 1. Submit with GWA exceeding limit
        $response = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->gwaScholarship->id,
                 'gwa' => 2.50, // exceeds 2.00
                 'document' => UploadedFile::fake()->create('cog.png', 100, 'image/png'),
             ]);

        $response->assertStatus(422);

        // 2. Submit with missing GWA
        $response2 = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->gwaScholarship->id,
                 'gwa' => '', // missing
                 'document' => UploadedFile::fake()->create('cog.png', 100, 'image/png'),
             ]);

        $response2->assertStatus(422);
    }

    /** @test */
    public function student_submitting_to_scholarship_with_no_gwa_limit_can_leave_gwa_blank()
    {
        $response = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->noGwaScholarship->id,
                 'gwa' => null, // Left optional/blank
                 'document' => UploadedFile::fake()->create('cog.png', 100, 'image/png'),
             ]);

        $response->assertStatus(200);
        $this->assertEquals(1, Application::count());
        
        $application = Application::first();
        $this->assertNull($application->gwa);
    }
}
