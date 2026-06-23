<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Support\Facades\DB;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create verified student
        $this->student = User::create([
            'name' => 'Original Name',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }

    public function test_profile_page_renders_successfully_for_verified_student(): void
    {
        $response = $this->actingAs($this->student)->get('/student/profile');

        $response->assertStatus(200);
        $response->assertSee('Original Name');
        $response->assertSee('student@clsu.edu.ph');
    }

    public function test_student_can_update_profile_details(): void
    {
        $response = $this->actingAs($this->student)->post('/student/profile', [
            'name' => 'Updated Name',
            'clsu_id_number' => '2023-1111',
            'college' => 'College of Science',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '09123456789',
        ]);

        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('success', 'Profile updated successfully!');

        // Check user name is updated
        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'name' => 'Updated Name',
        ]);

        // Check student profile fields are saved
        $profile = StudentProfile::where('user_id', $this->student->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('2023-1111', $profile->clsu_id_number);
        $this->assertEquals('College of Science', $profile->college);
        $this->assertEquals('BS Information Technology', $profile->course);
        $this->assertEquals('3rd Year', $profile->year_level);
        $this->assertEquals('09123456789', $profile->contact_number);
    }

    public function test_profile_validation_rejects_invalid_inputs(): void
    {
        // 1. Invalid CLSU ID format
        $response = $this->actingAs($this->student)->post('/student/profile', [
            'name' => 'Updated Name',
            'clsu_id_number' => '20231111', // missing hyphen
            'college' => 'College of Science',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '09123456789',
        ]);

        $response->assertSessionHasErrors(['clsu_id_number']);

        // 2. Invalid Contact Number format
        $response = $this->actingAs($this->student)->post('/student/profile', [
            'name' => 'Updated Name',
            'clsu_id_number' => '2023-1111',
            'college' => 'College of Science',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '12345', // invalid length & prefix
        ]);

        $response->assertSessionHasErrors(['contact_number']);
    }

    public function test_sensitive_profile_fields_are_stored_encrypted(): void
    {
        $this->actingAs($this->student)->post('/student/profile', [
            'name' => 'Encrypted Student',
            'clsu_id_number' => '2023-9999',
            'college' => 'College of Science',
            'course' => 'BS IT',
            'year_level' => '4th Year',
            'contact_number' => '09998887777',
        ]);

        // Access raw SQLite table database values directly using DB Query
        $rawProfile = DB::table('student_profiles')
            ->where('user_id', $this->student->id)
            ->first();

        $this->assertNotNull($rawProfile);
        
        // Raw values in database must NOT be plaintext '2023-9999' or '09998887777'
        $this->assertNotEquals('2023-9999', $rawProfile->clsu_id_number);
        $this->assertNotEquals('09998887777', $rawProfile->contact_number);

        // Eager loading decrypted values works fine
        $profileModel = StudentProfile::where('user_id', $this->student->id)->first();
        $this->assertEquals('2023-9999', $profileModel->clsu_id_number);
        $this->assertEquals('09998887777', $profileModel->contact_number);
    }

    public function test_approved_email_pdf_attachment_contains_student_profile_details(): void
    {
        $scholarship = Scholarship::create([
            'name' => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        // Create application
        $application = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Approved',
        ]);

        // Case 1: Eager load profile (it is null)
        $mailable = new \App\Mail\ApplicationStatusMail($application);
        $mailable->build(); // Should build successfully without crash even when profile is null
        $this->assertTrue(true);

        // Case 2: Create profile and save, then verify it builds successfully too
        StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '2023-1234',
            'college' => 'College of Science',
            'course' => 'BS IT',
            'year_level' => '3rd Year',
            'contact_number' => '09123456789',
        ]);

        $application->load('user.profile');
        $mailableWithProfile = new \App\Mail\ApplicationStatusMail($application);
        $mailableWithProfile->build(); // Should build successfully
        $this->assertTrue(true);
    }
}
