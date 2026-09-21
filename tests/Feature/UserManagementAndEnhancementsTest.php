<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\Announcement;
use App\Services\OfficeHoursService;
use Carbon\Carbon;

class UserManagementAndEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;
    protected User $staff;
    protected User $student;
    protected Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->director = User::create([
            'name' => 'Director SuperAdmin',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->staff = User::create([
            'name' => 'Staff Evaluator',
            'email' => 'evaluator@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Student Scholar',
            'email' => 'scholar@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '23-9999',
            'college' => 'College of Science',
            'course' => 'BS Computer Science',
            'year_level' => '3rd Year',
            'contact_number' => '09123456789',
            'emergency_contact_number' => '09998887777',
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'CLSU University Academic Grant',
            'description' => 'Full tuition and stipend grant for outstanding academic performance.',
            'min_gwa_required' => 1.75,
            'status' => 'Active',
        ]);

        $this->staff->scholarships()->attach($this->scholarship->id);
    }

    public function test_director_can_access_user_management_portal(): void
    {
        $response = $this->actingAs($this->director)->get(route('superadmin.users'));

        $response->assertStatus(200);
        $response->assertSee('Student Scholar');
        $response->assertSee('23-9999');
        $response->assertSee('BS Computer Science');
    }

    public function test_director_can_toggle_student_account_status(): void
    {
        $this->assertTrue($this->student->is_active);

        // Deactivate
        $response = $this->actingAs($this->director)->post(route('superadmin.users.toggle-status', $this->student->id));
        $response->assertStatus(302);

        $this->student->refresh();
        $this->assertFalse($this->student->is_active);

        // Reactivate
        $response = $this->actingAs($this->director)->post(route('superadmin.users.toggle-status', $this->student->id));
        $response->assertStatus(302);

        $this->student->refresh();
        $this->assertTrue($this->student->is_active);
    }

    public function test_director_can_reset_user_mfa(): void
    {
        $this->student->update([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($this->director)->post(route('superadmin.users.reset-mfa', $this->student->id));
        $response->assertStatus(302);

        $this->student->refresh();
        $this->assertNull($this->student->otp_code);
        $this->assertNull($this->student->otp_expires_at);
    }

    public function test_student_announcements_feed_displays_active_announcements(): void
    {
        Announcement::create([
            'title' => 'Midterm Evaluation Announcement',
            'content' => 'All scholars must submit mid-semester verification forms.',
            'author_id' => $this->director->id,
            'scheduled_publish_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->student)->get(route('student.announcements'));

        $response->assertStatus(200);
        $response->assertSee('Midterm Evaluation Announcement');
        $response->assertSee('All scholars must submit mid-semester verification forms.');
    }

    public function test_admin_can_revoke_approved_scholarship_grant(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Approved',
            'dpa_consent_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.revoke', $application->id), [
            'reason' => 'Student failed to meet the required semester GPA threshold.',
        ]);

        $response->assertStatus(302);

        $application->refresh();
        $this->assertEquals('Revoked', $application->status);
        $this->assertEquals('Student failed to meet the required semester GPA threshold.', $application->remarks);

        // Verify status log
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $application->id,
            'status' => 'Revoked',
        ]);
    }

    public function test_office_hours_service_detects_schedule(): void
    {
        // Set fixed daytime on a Wednesday: 10:00 AM PHT
        Carbon::setTestNow(Carbon::parse('2026-09-16 10:00:00', 'Asia/Manila'));
        $this->assertTrue(OfficeHoursService::isOfficeHours());

        // Set fixed nighttime on a Wednesday: 8:00 PM PHT
        Carbon::setTestNow(Carbon::parse('2026-09-16 20:00:00', 'Asia/Manila'));
        $this->assertFalse(OfficeHoursService::isOfficeHours());

        // Set weekend: Sunday 2:00 PM PHT
        Carbon::setTestNow(Carbon::parse('2026-09-20 14:00:00', 'Asia/Manila'));
        $this->assertFalse(OfficeHoursService::isOfficeHours());

        Carbon::setTestNow(); // Reset time mock
    }
}
