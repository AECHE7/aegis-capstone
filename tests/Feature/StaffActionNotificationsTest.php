<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\Document;
use App\Models\EmailLog;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\ApplicationSubmissionConfirmationNotification;
use App\Notifications\NewAnnouncementNotification;
use App\Notifications\NewApplicationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class StaffActionNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;
    protected User $student;
    protected Scholarship $scholarship;
    protected AcademicTerm $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'City Academic Excellence Grant',
            'description' => 'Merit-based financial assistance',
            'deadline' => now()->addDays(30),
            'status' => 'Active',
            'min_gwa_required' => 2.00,
        ]);
        $this->scholarship->staff()->attach($this->staff->id);
    }

    public function test_staff_approving_application_sends_database_notification_and_logs_email(): void
    {
        Mail::fake();

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Under Review',
            'gwa' => 1.50,
            'assigned_to' => $this->staff->id,
            'dpa_consent_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.updateStatus', $application->id), [
            'status' => 'Approved',
            'remarks' => 'All documentary requirements verified and approved.',
        ]);

        $response->assertStatus(302);

        // 1. Verify in-app database notification for student
        $this->assertEquals(1, $this->student->unreadNotifications()->count());
        $notification = $this->student->notifications()->first();
        $this->assertEquals('Application Status Update', $notification->data['title']);
        $this->assertEquals('Approved', $notification->data['status']);
        $this->assertStringContainsString("APP-{$application->id}", $notification->data['message']);

        // 2. Verify EmailLog created
        $this->assertDatabaseHas('email_logs', [
            'application_id' => $application->id,
            'recipient' => $this->student->email,
            'status' => 'sent',
        ]);
    }

    public function test_staff_returning_application_sends_database_notification_and_logs_email(): void
    {
        Mail::fake();

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Under Review',
            'gwa' => 1.75,
            'assigned_to' => $this->staff->id,
            'dpa_consent_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.updateStatus', $application->id), [
            'status' => 'Returned',
            'remarks' => 'Please provide a clear copy of your Certificate of Grades.',
        ]);

        $response->assertStatus(302);

        // 1. Verify in-app database notification for student
        $this->assertEquals(1, $this->student->unreadNotifications()->count());
        $notification = $this->student->notifications()->first();
        $this->assertEquals('Application Status Update', $notification->data['title']);
        $this->assertEquals('Returned', $notification->data['status']);
        $this->assertEquals('Please provide a clear copy of your Certificate of Grades.', $notification->data['remarks']);

        // 2. Verify EmailLog created
        $this->assertDatabaseHas('email_logs', [
            'application_id' => $application->id,
            'recipient' => $this->student->email,
            'status' => 'sent',
        ]);
    }

    public function test_staff_revoking_scholarship_sends_database_notification_and_logs_revocation_email(): void
    {
        Mail::fake();

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Approved',
            'gwa' => 1.50,
            'assigned_to' => $this->staff->id,
            'dpa_consent_at' => now(),
        ]);

        $reason = 'Student withdrew from the university enrollment roll.';
        $response = $this->actingAs($this->staff)->post(route('admin.revoke', $application->id), [
            'reason' => $reason,
        ]);

        $response->assertStatus(302);

        // 1. Verify in-app database notification for student
        $this->assertEquals(1, $this->student->unreadNotifications()->count());
        $notification = $this->student->notifications()->first();
        $this->assertEquals('Application Status Update', $notification->data['title']);
        $this->assertEquals('Revoked', $notification->data['status']);

        // 2. Verify EmailLog has revocation notice
        $this->assertDatabaseHas('email_logs', [
            'application_id' => $application->id,
            'recipient' => $this->student->email,
            'content' => "Grant revoked: {$reason}",
            'status' => 'sent',
        ]);
    }

    public function test_staff_creating_announcement_sends_notification_to_all_active_users(): void
    {
        $anotherStudent = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $response = $this->actingAs($this->staff)->postJson(route('admin.announcements.store'), [
            'title' => 'Midterm Stipend Release Schedule',
            'content' => 'Payout will commence this coming Monday at the Cashier Office.',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // Both students and staff should receive the announcement
        $this->assertEquals(1, $this->student->unreadNotifications()->count());
        $this->assertEquals(1, $anotherStudent->unreadNotifications()->count());

        $notification = $this->student->notifications()->first();
        $this->assertEquals('Official Announcement', $notification->data['title']);
        $this->assertEquals('Midterm Stipend Release Schedule', $notification->data['message']);
    }

    public function test_student_resubmission_sends_notification_to_assigned_staff(): void
    {
        Storage::fake('local');
        Queue::fake();

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Returned',
            'remarks' => 'Please upload a clearer copy of your Certificate of Grades.',
            'gwa' => 1.75,
            'assigned_to' => $this->staff->id,
            'dpa_consent_at' => now(),
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'document_type' => 'COG',
            'file_path' => 'documents/old_grades.pdf',
            'original_name' => 'old_grades.pdf',
            'uploaded_by' => $this->student->id,
        ]);

        $file = UploadedFile::fake()->create('corrected_grades.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->student)->post(route('student.application.reupload', $application->id), [
            'cog_file' => $file,
        ]);

        $response->assertRedirect(route('student.dashboard'));

        // Assigned staff should receive NewApplicationNotification for the resubmission
        $this->assertEquals(1, $this->staff->unreadNotifications()->count());
        $notification = $this->staff->notifications()->first();
        $this->assertEquals('New Application Submitted', $notification->data['title']);
        $this->assertEquals(route('admin.review', $application->id), $notification->data['url']);
    }

    public function test_staff_notification_center_and_read_unread_lifecycle(): void
    {
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'status' => 'Pending Verification',
            'gwa' => 1.50,
            'assigned_to' => $this->staff->id,
        ]);

        // Staff receives notification of new submission
        $this->staff->notify(new NewApplicationNotification($application));

        // 1. Staff retrieves notification summary badge JSON
        $badgeResponse = $this->actingAs($this->staff)->getJson('/notifications');
        $badgeResponse->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('unread_count', 1);

        $notificationId = $this->staff->notifications()->first()->id;

        // 2. Staff marks notification as read
        $readResponse = $this->actingAs($this->staff)->postJson("/notifications/{$notificationId}/read");
        $readResponse->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(0, $this->staff->unreadNotifications()->count());

        // 3. Staff loads full notification center HTML view
        $centerResponse = $this->actingAs($this->staff)->get('/notifications?view=center');
        $centerResponse->assertStatus(200)
            ->assertSee('Notifications &amp; Alerts Center', false);
    }
}
