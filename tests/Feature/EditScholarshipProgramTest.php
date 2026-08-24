<?php

namespace Tests\Feature;

use App\Models\Scholarship;
use App\Models\ScholarshipField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EditScholarshipProgramTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $admin;
    private User $student;
    private Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Original Scholarship',
            'description' => 'Original description here.',
            'min_gwa_required' => 2.00,
            'max_renewals' => 4,
            'status' => 'Active',
        ]);

        $this->scholarship->fields()->create([
            'field_name' => 'household_income',
            'field_label' => 'Household Income',
            'field_type' => 'number',
            'is_required' => true,
        ]);
    }

    #[Test]
    public function superadmin_can_retrieve_scholarship_details_as_json()
    {
        $response = $this->actingAs($this->superadmin)
                         ->getJson(route('superadmin.scholarships.show', $this->scholarship->id));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('scholarship.name', 'Original Scholarship');
        $response->assertJsonCount(1, 'scholarship.fields');
    }

    #[Test]
    public function superadmin_can_update_scholarship_details_and_custom_fields()
    {
        $response = $this->actingAs($this->superadmin)
                         ->putJson(route('superadmin.scholarships.update', $this->scholarship->id), [
                             'name' => 'Updated Scholarship Name',
                             'description' => 'Updated program description.',
                             'min_gwa_required' => 1.75,
                             'max_renewals' => 6,
                             'fields' => [
                                 [
                                     'label' => 'Father Occupation',
                                     'type' => 'text',
                                     'required' => '1',
                                 ],
                                 [
                                     'label' => 'Year Level Choice',
                                     'type' => 'select',
                                     'required' => '0',
                                     'options' => '1st Year, 2nd Year, 3rd Year',
                                 ],
                                 [
                                     'label' => 'Birthdate',
                                     'type' => 'date',
                                     'required' => '1',
                                 ],
                                 [
                                     'label' => 'Secondary Contact Email',
                                     'type' => 'email',
                                     'required' => '0',
                                 ]
                             ]
                         ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->scholarship->refresh();
        $this->assertEquals('Updated Scholarship Name', $this->scholarship->name);
        $this->assertEquals('Updated program description.', $this->scholarship->description);
        $this->assertEquals(1.75, $this->scholarship->min_gwa_required);
        $this->assertEquals(6, $this->scholarship->max_renewals);

        // Verify fields were deleted and updated
        $this->assertEquals(4, $this->scholarship->fields()->count());
        $this->assertTrue($this->scholarship->fields()->where('field_label', 'Father Occupation')->first()->is_required);
        $this->assertFalse($this->scholarship->fields()->where('field_label', 'Year Level Choice')->first()->is_required);
        $this->assertEquals(['1st Year', '2nd Year', '3rd Year'], $this->scholarship->fields()->where('field_label', 'Year Level Choice')->first()->options);
        $this->assertTrue($this->scholarship->fields()->where('field_label', 'Birthdate')->first()->is_required);
        $this->assertEquals('date', $this->scholarship->fields()->where('field_label', 'Birthdate')->first()->field_type);
        $this->assertEquals('email', $this->scholarship->fields()->where('field_label', 'Secondary Contact Email')->first()->field_type);
    }

    #[Test]
    public function superadmin_can_update_scholarship_with_null_gwa_requirement()
    {
        $response = $this->actingAs($this->superadmin)
                         ->putJson(route('superadmin.scholarships.update', $this->scholarship->id), [
                             'name' => 'No GWA Scholarship',
                             'description' => 'GWA is optional.',
                             'min_gwa_required' => null,
                             'max_renewals' => 4,
                         ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->scholarship->refresh();
        $this->assertNull($this->scholarship->min_gwa_required);
    }

    #[Test]
    public function non_superadmins_are_blocked_from_retrieving_and_updating_scholarships()
    {
        // Admin
        $this->actingAs($this->admin)
             ->getJson(route('superadmin.scholarships.show', $this->scholarship->id))
             ->assertStatus(403);

        $this->actingAs($this->admin)
             ->putJson(route('superadmin.scholarships.update', $this->scholarship->id), ['name' => 'Hack'])
             ->assertStatus(403);

        // Student
        $this->actingAs($this->student)
             ->getJson(route('superadmin.scholarships.show', $this->scholarship->id))
             ->assertStatus(403);

        // Guest
        $this->get(route('superadmin.scholarships.show', $this->scholarship->id))
             ->assertStatus(403);
    }
}
