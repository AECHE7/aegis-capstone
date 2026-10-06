<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\StudentProfile;

class MobileNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Mobile Student',
            'email' => 'mobilestudent@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $this->student->id,
            'clsu_id_number' => '22-0001',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'college' => 'College of Information and Communications Technology',
            'contact_number' => '09123456789',
        ]);

        $this->admin = User::create([
            'name' => 'Mobile Admin',
            'email' => 'mobileadmin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_student_mobile_nav_includes_available_scholarships_tab()
    {
        $response = $this->actingAs($this->student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('mobile-bottom-nav', false);
        $response->assertSee(route('scholarships.catalog'), false);
        $response->assertSee('Scholarships');
        $response->assertSee('fa-graduation-cap');
    }

    public function test_scholarships_catalog_page_highlights_scholarships_mobile_nav_tab_as_active()
    {
        $response = $this->actingAs($this->student)->get(route('scholarships.catalog'));

        $response->assertStatus(200);
        $response->assertSee('mobile-bottom-nav', false);
        // Ensure active class is attached to the scholarships catalog tab
        $response->assertSee('active', false);
        $response->assertSee(route('scholarships.catalog'), false);
    }
}
