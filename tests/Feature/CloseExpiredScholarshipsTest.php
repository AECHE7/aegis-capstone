<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CloseExpiredScholarshipsTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Scholarship $expiredScholarship;
    private Scholarship $activeScholarship;
    private AcademicTerm $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicTerm = AcademicTerm::create([
            'academic_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Expired scholarship (deadline in past)
        $this->expiredScholarship = Scholarship::create([
            'name' => 'Expired Merit Scholarship',
            'description' => 'Test',
            'min_gwa_required' => 2.00,
            'deadline' => Carbon::yesterday(),
            'status' => 'Active',
        ]);

        // Active scholarship (deadline in future)
        $this->activeScholarship = Scholarship::create([
            'name' => 'Future Merit Scholarship',
            'description' => 'Test',
            'min_gwa_required' => 2.00,
            'deadline' => Carbon::tomorrow(),
            'status' => 'Active',
        ]);
    }

    public function test_command_closes_expired_scholarship_and_archives_pending_applications(): void
    {
        // Create pending application for expired scholarship
        $expiredApp = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->expiredScholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->expiredScholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
            'is_archived' => false,
        ]);

        // Create pending application for active scholarship
        $activeApp = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->activeScholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->activeScholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
            'is_archived' => false,
        ]);

        // Run the command
        Artisan::call('scholarships:close-expired');

        // Check expired scholarship is closed
        $this->expiredScholarship->refresh();
        $this->assertEquals('Closed', $this->expiredScholarship->status);

        // Check active scholarship remains active
        $this->activeScholarship->refresh();
        $this->assertEquals('Active', $this->activeScholarship->status);

        // Check expired application is archived
        $expiredApp->refresh();
        $this->assertTrue($expiredApp->is_archived);

        // Check active application remains active (not archived)
        $activeApp->refresh();
        $this->assertFalse($activeApp->is_archived);
    }

    public function test_scheduler_web_trigger_endpoint_executes_successfully(): void
    {
        // Test with invalid key
        $response = $this->get('/scheduler/run?key=wrong_key');
        $response->assertStatus(403);

        // Test with valid key
        $validKey = env('SCHEDULER_KEY', 'aegis_cron_secret');
        $response = $this->get("/scheduler/run?key={$validKey}");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Scheduler run complete.',
        ]);

        // Check expired scholarship is closed via trigger
        $this->expiredScholarship->refresh();
        $this->assertEquals('Closed', $this->expiredScholarship->status);
    }
}
