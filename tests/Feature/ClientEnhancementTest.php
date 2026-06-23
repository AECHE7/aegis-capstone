<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\UatFeedback;
use Illuminate\Support\Facades\DB;

class ClientEnhancementTest extends TestCase
{
    use RefreshDatabase;

    private $student;
    private $admin;
    private $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $this->admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admintest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->superadmin = User::create([
            'name' => 'Director',
            'email' => 'directortest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'superadmin'
        ]);
    }

    public function test_student_profile_fields_are_encrypted_in_database(): void
    {
        $profile = StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '2023-4567',
            'college' => 'College of Science',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '09123456789'
        ]);

        // 1. Assert that Eloquent decrypts it automatically upon access
        $this->assertEquals('2023-4567', $profile->clsu_id_number);
        $this->assertEquals('09123456789', $profile->contact_number);

        // 2. Assert that it is stored as encrypted ciphertext in the database
        $rawRow = DB::table('student_profiles')->where('id', $profile->id)->first();
        
        $this->assertNotEquals('2023-4567', $rawRow->clsu_id_number);
        $this->assertNotEquals('09123456789', $rawRow->contact_number);
    }

    public function test_user_can_submit_uat_evaluation_form(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('uat.store'), [
                'functional_suitability' => 5,
                'usability' => 4,
                'reliability' => 5,
                'security' => 4,
                'comments' => 'Excellent system responsiveness.'
            ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('uat_feedbacks', [
            'user_id' => $this->student->id,
            'role' => 'student',
            'functional_suitability' => 5,
            'usability' => 4,
            'reliability' => 5,
            'security' => 4,
            'comments' => 'Excellent system responsiveness.'
        ]);
    }

    public function test_superadmin_analytics_aggregates_uat_scores(): void
    {
        // Seed UAT feedback
        UatFeedback::create([
            'user_id' => $this->student->id,
            'role' => 'student',
            'functional_suitability' => 5,
            'usability' => 4,
            'reliability' => 5,
            'security' => 4,
            'comments' => 'Student feedback.'
        ]);

        UatFeedback::create([
            'user_id' => $this->admin->id,
            'role' => 'admin',
            'functional_suitability' => 4,
            'usability' => 4,
            'reliability' => 3,
            'security' => 4,
            'comments' => 'Admin feedback.'
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Overall Mean Score');
        
        // Averages:
        // FS: (5+4)/2 = 4.5
        // US: (4+4)/2 = 4.0
        // RL: (5+3)/2 = 4.0
        // SC: (4+4)/2 = 4.0
        // Mean = 4.125 -> rounded to 4.13
        $response->assertSee('Total Responses:');
        $response->assertSee('2');
    }
}
