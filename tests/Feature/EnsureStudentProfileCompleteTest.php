<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;

class EnsureStudentProfileCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'dpa_consent_at' => now(),
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'University Academic Grant',
            'min_gwa_required' => 1.75,
            'status' => 'Active',
        ]);
    }

    public function test_student_with_incomplete_profile_is_redirected_to_profile_page(): void
    {
        // Deliberately delete profile to simulate incomplete state
        $this->student->profile()?->delete();
        $this->student->refresh();
        $this->assertFalse($this->student->isProfileComplete());

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));
        $response->assertRedirect(route('student.profile'));
        $response->assertSessionHas('warning');

        $applyResponse = $this->actingAs($this->student)->get(route('student.apply'));
        $applyResponse->assertRedirect(route('student.profile'));
    }

    public function test_ajax_request_by_student_with_incomplete_profile_returns_403_json(): void
    {
        $this->student->profile()?->delete();
        $this->student->refresh();
        $this->assertFalse($this->student->isProfileComplete());

        $response = $this->actingAs($this->student)->postJson(route('student.complete-tour'));
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Profile incomplete. Please complete your basic student information before proceeding.',
            'redirect' => route('student.profile'),
        ]);
    }

    public function test_exempt_routes_are_accessible_with_incomplete_profile(): void
    {
        $this->student->profile()?->delete();
        $this->student->refresh();
        $this->assertFalse($this->student->isProfileComplete());

        // Profile edit page must be accessible so student can fill it
        $this->actingAs($this->student)->get(route('student.profile'))->assertStatus(200);

        // Security settings must be accessible
        $this->actingAs($this->student)->get(route('profile.security'))->assertStatus(200);

        // Notification polling must be accessible
        $this->actingAs($this->student)->getJson(route('notifications.index'))->assertStatus(200);
    }

    public function test_completed_profile_grants_full_access(): void
    {
        // Student created via factory has auto-provisioned complete profile in test environment
        $this->assertTrue($this->student->isProfileComplete());

        $response = $this->actingAs($this->student)->get(route('student.dashboard'));
        $response->assertStatus(200);

        $applyResponse = $this->actingAs($this->student)->get(route('student.apply'));
        $applyResponse->assertStatus(200);
    }
}
