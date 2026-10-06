<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;

class StaffIncomingAndRejectedArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $student;
    protected Scholarship $scholarship;
    protected AcademicTerm $term;

    protected function setUp(): void
    {
        parent::setUp();

        $this->term = AcademicTerm::create([
            'academic_year' => '2026-2027',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Institutional Academic Grant',
            'min_gwa_required' => 1.75,
            'status' => 'Active',
            'max_renewals' => 4,
        ]);

        $this->staff = User::create([
            'name' => 'Evaluation Staff',
            'email' => 'evaluator@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Applicant Student',
            'email' => 'applicant@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);
    }

    public function test_staff_with_no_assigned_scholarships_can_view_incoming_unassigned_applications(): void
    {
        // Application is incoming and unassigned (assigned_to = null)
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
            'assigned_to' => null,
            'is_archived' => false,
        ]);

        // Staff visits dashboard without query params
        $response = $this->actingAs($this->staff)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('APP-' . $app->id);
        $response->assertSee('Applicant Student');
    }

    public function test_rejecting_an_application_automatically_archives_it(): void
    {
        $app = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
            'assigned_to' => null,
            'is_archived' => false,
        ]);

        $this->assertFalse($app->is_archived);

        // Staff rejects the application
        $response = $this->actingAs($this->staff)->post(route('admin.updateStatus', $app->id), [
            'status' => 'Rejected',
            'remarks' => 'Ineligible credentials provided.',
        ]);

        $response->assertStatus(302);
        $app->refresh();

        $this->assertEquals('Rejected', $app->status);
        $this->assertTrue($app->is_archived);

        // Flush session to clear flash message before checking active dashboard
        session()->flush();

        // Active queue without filters should NOT contain the archived rejected app
        $activeResponse = $this->actingAs($this->staff)->get(route('admin.dashboard'));
        $activeResponse->assertStatus(200);
        $this->assertFalse($activeResponse->viewData('applications')->contains('id', $app->id));

        // Filtering by status=Rejected OR archived=1 SHOULD display the rejected application
        $rejectedFilterResponse = $this->actingAs($this->staff)->get(route('admin.dashboard', ['status' => 'Rejected']));
        $rejectedFilterResponse->assertStatus(200);
        $this->assertTrue($rejectedFilterResponse->viewData('applications')->contains('id', $app->id));
        $rejectedFilterResponse->assertSee('APP-' . $app->id);

        $archiveFilterResponse = $this->actingAs($this->staff)->get(route('admin.dashboard', ['archived' => '1']));
        $archiveFilterResponse->assertStatus(200);
        $this->assertTrue($archiveFilterResponse->viewData('applications')->contains('id', $app->id));
        $archiveFilterResponse->assertSee('APP-' . $app->id);
    }

    public function test_bulk_reject_automatically_archives_applications(): void
    {
        $app1 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->term->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.applications.bulk-action'), [
            'application_ids' => [$app1->id],
            'status' => 'Rejected',
            'remarks' => 'Bulk rejected due to quota limit.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $app1->refresh();

        $this->assertEquals('Rejected', $app1->status);
        $this->assertTrue($app1->is_archived);
    }

    public function test_database_seeder_does_not_duplicate_scholarships_if_already_present(): void
    {
        $initialCount = Scholarship::withTrashed()->count();
        $this->assertGreaterThan(0, $initialCount);

        // Run the DatabaseSeeder
        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();

        // Scholarship count should remain unchanged because scholarships already existed
        $this->assertEquals($initialCount, Scholarship::withTrashed()->count());
    }
}
