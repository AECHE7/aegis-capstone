<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\AcademicTerm;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\NewApplicationNotification;

class AdvancedPortalWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    private $student;
    private $admin;
    private $superadmin;
    private $scholarship;
    private $academicTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Juan Student',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->superadmin = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Barangay Scholar',
            'description' => 'For barangay official descendants',
            'min_gwa_required' => 2.50,
            'status' => 'Active',
        ]);

        $this->academicTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);
    }

    public function test_deactivated_staff_cannot_login(): void
    {
        // 1. Attempt login with active staff
        $response = $this->post('/login', [
            'email' => 'staff@clsu.edu.ph',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        $this->post('/logout');
        $this->assertGuest();

        // 2. Deactivate staff and try to log in
        $this->admin->is_active = false;
        $this->admin->save();

        $response = $this->post('/login', [
            'email' => 'staff@clsu.edu.ph',
            'password' => 'password',
        ]);
        
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_superadmin_can_invite_staff_with_assignments(): void
    {
        $this->actingAs($this->superadmin);

        // Invite new staff and assign to Barangay Scholar
        $response = $this->post(route('superadmin.staff.invite'), [
            'name' => 'Alice Evaluator',
            'email' => 'alice@clsu.edu.ph',
            'scholarship_ids' => [$this->scholarship->id],
        ]);

        $response->assertStatus(302);
        
        $newStaff = User::where('email', 'alice@clsu.edu.ph')->first();
        $this->assertNotNull($newStaff);
        $this->assertTrue($newStaff->scholarships->contains($this->scholarship->id));
    }

    public function test_superadmin_can_revoke_and_reactivate_staff(): void
    {
        $this->actingAs($this->superadmin);

        // Revoke
        $response = $this->post(route('superadmin.staff.revoke', $this->admin->id));
        $response->assertStatus(302);
        $this->admin->refresh();
        $this->assertFalse($this->admin->is_active);

        // Reactivate
        $response = $this->post(route('superadmin.staff.reactivate', $this->admin->id));
        $response->assertStatus(302);
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }

    public function test_admin_can_archive_and_unarchive_applications(): void
    {
        $this->actingAs($this->admin);

        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $application->refresh();
        // 1. Initially is_archived is false
        $this->assertFalse($application->is_archived);

        // 2. Archive it
        $response = $this->post(route('admin.archive', $application->id));
        $response->assertRedirect(route('admin.dashboard'));
        
        $application->refresh();
        $this->assertTrue($application->is_archived);

        // 3. Unarchive it
        $response = $this->post(route('admin.unarchive', $application->id));
        $response->assertRedirect(route('admin.dashboard'));

        $application->refresh();
        $this->assertFalse($application->is_archived);
    }

    public function test_applications_queue_filtering_by_staff_assignments(): void
    {
        // Create another scholarship
        $otherScholarship = Scholarship::create([
            'name' => 'DOST Scholar',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        // Application for Barangay Scholar
        $app1 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
        ]);

        // Application for DOST Scholar
        $app2 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $otherScholarship->id,
            'academic_term_id' => $this->academicTerm->id,
            'program_name' => $otherScholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
        ]);

        // Assign staff to Barangay Scholar only
        $this->admin->scholarships()->sync([$this->scholarship->id]);

        $this->actingAs($this->admin);

        // Load dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Staff should see app1 but NOT app2
        $applications = $response->viewData('applications');
        $this->assertTrue($applications->contains($app1));
        $this->assertFalse($applications->contains($app2));

        // Superadmin/Director should see both
        $this->actingAs($this->superadmin);
        $response2 = $this->get(route('admin.dashboard'));
        $applications2 = $response2->viewData('applications');
        $this->assertTrue($applications2->contains($app1));
        $this->assertTrue($applications2->contains($app2));
    }

    public function test_notifications_are_dispatched_and_manageable(): void
    {
        // 1. Submit Application and notify assigned staff
        $this->admin->scholarships()->sync([$this->scholarship->id]);

        $this->actingAs($this->student);
        Storage::fake('local');
        $fakeImg = UploadedFile::fake()->create('cog.jpg', 100, 'image/jpeg');

        $response = $this->post(route('student.store'), [
            'scholarship_id' => $this->scholarship->id,
            'gwa' => 1.50,
            'document' => $fakeImg,
        ]);

        $response->assertRedirect(route('student.dashboard'));
        
        // Assigned staff should have 1 unread notification
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());
        $notification = $this->admin->unreadNotifications()->first();
        $this->assertEquals('New Application Submitted', $notification->data['title']);

        // 2. Fetch notifications JSON
        $this->actingAs($this->admin);
        $responseJson = $this->get(route('notifications.index'));
        $responseJson->assertStatus(200);
        $responseJson->assertJsonFragment(['title' => 'New Application Submitted']);

        // 3. Mark notification as read
        $responseRead = $this->post(route('notifications.read', $notification->id));
        $responseRead->assertStatus(200);
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());

        // 4. Update application status and notify student
        $application = Application::latest()->first();
        
        $this->actingAs($this->admin);
        $responseStatus = $this->post(route('admin.updateStatus', $application->id), [
            'status' => 'Approved',
            'remarks' => 'Valid grades.',
        ]);
        
        // Student should have 1 unread notification
        $this->assertEquals(1, $this->student->unreadNotifications()->count());
        $studentNotif = $this->student->unreadNotifications()->first();
        $this->assertEquals('Application Status Update', $studentNotif->data['title']);
        $this->assertEquals('Approved', $studentNotif->data['status']);
    }

    public function test_dynamic_form_builder_and_encrypted_field_responses(): void
    {
        // 1. Create a scholarship program with custom fields
        $this->actingAs($this->superadmin);
        $response = $this->post(route('superadmin.scholarships.store'), [
            'name' => 'Elite Athlete Grant',
            'min_gwa_required' => 3.00,
            'description' => 'For national athletes',
            'fields' => [
                [
                    'label' => 'Primary Sport',
                    'type' => 'text',
                    'required' => '1',
                ],
                [
                    'label' => 'Annual Income',
                    'type' => 'number',
                    'required' => '0',
                ]
            ]
        ]);

        $response->assertStatus(302);
        $scholarship = Scholarship::where('name', 'Elite Athlete Grant')->first();
        $this->assertNotNull($scholarship);
        $this->assertEquals(2, $scholarship->fields()->count());

        // 2. Fetch fields endpoint
        $this->actingAs($this->student);
        $responseFields = $this->get(route('scholarships.fields', $scholarship->id));
        $responseFields->assertStatus(200);
        $responseFields->assertJsonCount(2);

        // 3. Submit application with dynamic custom fields
        Storage::fake('local');
        $fakeImg = UploadedFile::fake()->create('cog2.jpg', 100, 'image/jpeg');
        
        $sportField = $scholarship->fields()->where('field_type', 'text')->first();
        $incomeField = $scholarship->fields()->where('field_type', 'number')->first();

        $responseStore = $this->post(route('student.store'), [
            'scholarship_id' => $scholarship->id,
            'gwa' => 2.00,
            'document' => $fakeImg,
            'custom_fields' => [
                $sportField->field_name => 'Basketball',
                $incomeField->field_name => '250000',
            ]
        ]);

        $responseStore->assertRedirect(route('student.dashboard'));
        
        $application = Application::where('scholarship_id', $scholarship->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals(2, $application->customFields()->count());

        // Check encryption in database
        $basketballFieldResponse = $application->customFields()->where('field_name', 'Primary Sport')->first();
        $this->assertEquals('Basketball', $basketballFieldResponse->field_value); // Eloquent decrypts automatically

        $rawRow = DB::table('application_fields')->where('id', $basketballFieldResponse->id)->first();
        $this->assertNotEquals('Basketball', $rawRow->field_value); // Stored encrypted at rest

        // 4. Admin review page displays responses
        $this->actingAs($this->admin);
        $responseReview = $this->get(route('admin.review', $application->id));
        $responseReview->assertStatus(200);
        $responseReview->assertSee('Primary Sport');
        $responseReview->assertSee('Basketball');
    }

    public function test_student_cannot_access_unauthorized_endpoints(): void
    {
        $this->actingAs($this->student);

        // 1. Cannot access Admin dashboard
        $responseAdmin = $this->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(403);

        // 2. Cannot access Super Admin scholarships
        $responseSuper = $this->get(route('superadmin.scholarships'));
        $responseSuper->assertStatus(403);

        // 3. Create another student and their document
        $anotherStudent = User::create([
            'name' => 'Another Student',
            'email' => 'another@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);

        $app = Application::create([
            'user_id' => $anotherStudent->id,
            'scholarship_id' => $this->scholarship->id,
            'program_name' => $this->scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $document = \App\Models\Document::create([
            'application_id' => $app->id,
            'file_path' => 'mock/path/doc.jpg',
            'original_name' => 'cog.jpg',
            'document_type' => 'COG'
        ]);

        // Student cannot access another student's document image
        $responseDoc = $this->get(route('document.view', $document->id));
        $responseDoc->assertStatus(403);
    }
}

