<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Setting;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\Application;
use Illuminate\Support\Facades\Cache;

class AcademicTermManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $student;
    protected AcademicTerm $term1;
    protected AcademicTerm $term2;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->superadmin = User::factory()->create(['role' => 'superadmin', 'email_verified_at' => now()]);
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);

        Setting::set('app_name', 'A.E.G.I.S.');
        Setting::set('university_name', 'Central Luzon State University');

        $this->term1 = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);

        $this->term2 = AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function superadmin_can_view_settings_with_terms_and_active_semester_highlighted()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.settings'));

        $response->assertStatus(200);
        $response->assertSeeText('Academic Terms & Current Semester', false);
        $response->assertSeeText('1st Semester, Academic Year 2025-2026', false);
        $response->assertSeeText('CURRENT ACTIVE SEMESTER', false);
    }

    #[Test]
    public function superadmin_can_create_new_academic_term()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2026-2027',
                'set_active' => '0',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('academic_terms', [
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => false,
        ]);
    }

    #[Test]
    public function superadmin_can_create_new_academic_term_and_set_active_immediately()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2026-2027',
                'set_active' => '1',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // New term should be active
        $this->assertDatabaseHas('academic_terms', [
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        // Previous active term should now be inactive
        $this->assertFalse($this->term1->fresh()->is_active);
    }

    #[Test]
    public function cannot_create_duplicate_academic_term()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2025-2026',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function superadmin_can_switch_active_academic_term()
    {
        // Cache the active term first
        Cache::put('active_academic_term', $this->term1, 300);
        $this->assertTrue($this->term1->fresh()->is_active);
        $this->assertFalse($this->term2->fresh()->is_active);

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.terms.activate', $this->term2->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($this->term1->fresh()->is_active);
        $this->assertTrue($this->term2->fresh()->is_active);

        // Cache must have been busted
        $activeCached = Cache::get('active_academic_term');
        $this->assertTrue(is_null($activeCached) || $activeCached->id === $this->term2->id);
    }

    #[Test]
    public function cannot_delete_current_active_academic_term()
    {
        $response = $this->actingAs($this->superadmin)
            ->delete(route('superadmin.terms.destroy', $this->term1->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('academic_terms', ['id' => $this->term1->id]);
    }

    #[Test]
    public function cannot_delete_academic_term_with_existing_applications()
    {
        $scholarship = Scholarship::create(['name' => 'Merit Grant', 'min_gwa_required' => 1.75, 'status' => 'Active']);

        Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $this->term2->id,
            'program_name' => $scholarship->name,
            'gwa' => 1.5,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('superadmin.terms.destroy', $this->term2->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('academic_terms', ['id' => $this->term2->id]);
    }

    #[Test]
    public function superadmin_can_delete_unused_inactive_academic_term()
    {
        $unusedTerm = AcademicTerm::create([
            'semester' => 'Midyear',
            'academic_year' => '2024-2025',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('superadmin.terms.destroy', $unusedTerm->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('academic_terms', ['id' => $unusedTerm->id]);
    }

    #[Test]
    public function non_superadmin_cannot_manage_academic_terms()
    {
        // Admin
        $this->actingAs($this->admin)
            ->post(route('superadmin.terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2027-2028',
            ])->assertStatus(403);

        $this->actingAs($this->admin)
            ->post(route('superadmin.terms.activate', $this->term2->id))
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->delete(route('superadmin.terms.destroy', $this->term2->id))
            ->assertStatus(403);

        // Student
        $this->actingAs($this->student)
            ->post(route('superadmin.terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2027-2028',
            ])->assertStatus(403);
    }
}
