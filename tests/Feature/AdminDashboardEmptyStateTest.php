<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardEmptyStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@clsu.edu.ph'
        ]);
    }

    /**
     * Test empty state shows when no applications exist
     *
     * @return void
     */
    public function test_empty_state_shows_with_no_applications()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('No Applications Match Your Filters');
        $response->assertSee('fa-filter-circle-xmark');
    }

    /**
     * Test empty state shows correct message when filters applied
     *
     * @return void
     */
    public function test_empty_state_with_filters_shows_clear_button()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard?status=Approved&search=nonexistent');

        $response->assertStatus(200);
        $response->assertSee('No Applications Match Your Filters');
        $response->assertSee('Try adjusting your search criteria');
        $response->assertSee('Clear All Filters');
    }

    /**
     * Test empty state without filters shows different message
     *
     * @return void
     */
    public function test_empty_state_without_filters_shows_generic_message()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('There are currently no applications in this queue');
        $response->assertDontSee('Clear All Filters');
    }

    /**
     * Test clear filters button redirects correctly
     *
     * @return void
     */
    public function test_clear_filters_button_has_correct_route()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard?status=Approved');

        $response->assertStatus(200);
        $response->assertSee(route('admin.dashboard'), false);
    }

    /**
     * Test refresh button exists in empty state
     *
     * @return void
     */
    public function test_refresh_button_exists_in_filtered_empty_state()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard?search=test');

        $response->assertStatus(200);
        $response->assertSee('Refresh');
        $response->assertSee('fa-arrows-rotate');
        $response->assertSee('reloadQueue()', false);
    }

    /**
     * Test empty state styling elements exist
     *
     * @return void
     */
    public function test_empty_state_has_enhanced_styling()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('width:96px;height:96px', false);
        $response->assertSee('border-radius:50%', false);
        $response->assertSee('linear-gradient(135deg', false);
    }

    /**
     * Test empty state does NOT show when applications exist
     *
     * @return void
     */
    public function test_empty_state_not_shown_when_applications_exist()
    {
        // Create a test application
        Application::factory()->create([
            'status' => 'Pending',
            'user_id' => User::factory()->create(['role' => 'student'])
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('No Applications Match Your Filters');
    }

    /**
     * Test empty state respects request parameters
     *
     * @return void
     */
    public function test_empty_state_detects_multiple_filter_types()
    {
        $params = [
            'search' => 'test',
            'scholarship' => '1',
            'status' => 'Approved',
            'academic_term' => '1'
        ];

        $response = $this->actingAs($this->admin)->get('/admin/dashboard?' . http_build_query($params));

        $response->assertStatus(200);
        $response->assertSee('Clear All Filters');
    }
}
