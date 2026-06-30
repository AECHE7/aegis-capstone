<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\ApplicationField;
use Illuminate\Support\Facades\Storage;

class StudentPortalDetailsTest extends TestCase
{
    use RefreshDatabase;

    private $studentA;
    private $studentB;
    private $admin;
    private $scholarship;
    private $applicationA;
    private $fieldA;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        // Create Students
        $this->studentA = User::create([
            'name' => 'Student A',
            'email' => 'studenta@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->studentB = User::create([
            'name' => 'Student B',
            'email' => 'studentb@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create Admin
        $this->admin = User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Scholarship
        $this->scholarship = Scholarship::create([
            'name' => 'DOST Grant',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        // Create Application for Student A
        $this->applicationA = Application::create([
            'user_id' => $this->studentA->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        // Write a fake file to local storage
        Storage::disk('local')->put('uploads/test_custom.pdf', 'fake PDF content');

        // Create a custom field response
        $this->fieldA = ApplicationField::create([
            'application_id' => $this->applicationA->id,
            'field_name' => 'Income Certificate',
            'field_value' => 'uploads/test_custom.pdf',
        ]);
    }

    /**
     * Test that student can view their own details on the dashboard.
     */
    public function test_student_can_view_own_application_details(): void
    {
        $response = $this->actingAs($this->studentA)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Submitted Application Details');
        $response->assertSee('DOST Grant');
        $response->assertSee('1.75');
        $response->assertSee('Income Certificate');
    }

    /**
     * Test student can access their own custom uploaded file.
     */
    public function test_student_can_access_own_custom_uploaded_file(): void
    {
        $response = $this->actingAs($this->studentA)->get(route('application-field.file', $this->fieldA->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Test student cannot access another student's custom uploaded file.
     */
    public function test_student_cannot_access_other_student_custom_uploaded_file(): void
    {
        $response = $this->actingAs($this->studentB)->get(route('application-field.file', $this->fieldA->id));

        $response->assertStatus(403);
    }

    /**
     * Test unassigned admin is blocked from accessing custom uploaded file.
     */
    public function test_unassigned_admin_cannot_access_custom_uploaded_file(): void
    {
        // Admin is not assigned to $this->scholarship
        $response = $this->actingAs($this->admin)->get(route('application-field.file', $this->fieldA->id));

        $response->assertStatus(403);
    }

    /**
     * Test assigned admin can access custom uploaded file.
     */
    public function test_assigned_admin_can_access_custom_uploaded_file(): void
    {
        // Assign scholarship to admin
        $this->admin->scholarships()->attach($this->scholarship->id);

        $response = $this->actingAs($this->admin)->get(route('application-field.file', $this->fieldA->id));

        $response->assertStatus(200);
    }
}
