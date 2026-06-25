<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use Illuminate\Support\Facades\Cache;

class QueryCachingTest extends TestCase
{
    use RefreshDatabase;

    private $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);
    }

    /**
     * Test that loading the student application form caches active scholarships.
     */
    public function test_loading_apply_page_caches_active_scholarships(): void
    {
        Cache::forget('active_scholarships_list');
        $this->assertFalse(Cache::has('active_scholarships_list'));

        // Create an active scholarship
        $scholarship = Scholarship::create([
            'name' => 'Barangay Scholar Test',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        // Access the apply page
        $response = $this->actingAs($this->student)->get(route('student.apply'));
        $response->assertStatus(200);

        // Verify it is cached
        $this->assertTrue(Cache::has('active_scholarships_list'));
        $cachedList = Cache::get('active_scholarships_list');
        $this->assertCount(1, $cachedList);
        $this->assertEquals('Barangay Scholar Test', $cachedList->first()->name);
    }

    /**
     * Test that updating a scholarship busts the active scholarships cache.
     */
    public function test_saving_scholarship_busts_active_scholarships_cache(): void
    {
        $scholarship = Scholarship::create([
            'name' => 'Scholarship A',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);
        
        $this->actingAs($this->student)->get(route('student.apply'));
        $this->assertTrue(Cache::has('active_scholarships_list'));

        // Update the scholarship
        $scholarship->update(['status' => 'Inactive']);

        // Cache must be invalidated
        $this->assertFalse(Cache::has('active_scholarships_list'));
    }

    /**
     * Test that updating an academic term busts the active term cache.
     */
    public function test_saving_academic_term_busts_active_term_cache(): void
    {
        $term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true
        ]);

        Cache::remember('active_academic_term', 86400, function () use ($term) {
            return AcademicTerm::where('is_active', true)->first();
        });

        $this->assertTrue(Cache::has('active_academic_term'));

        // Update the term
        $term->update(['is_active' => false]);

        // Cache must be invalidated
        $this->assertFalse(Cache::has('active_academic_term'));
    }
}
