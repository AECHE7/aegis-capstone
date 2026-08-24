<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Scholarship;
use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApplyNullableGwaTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Scholarship $scholarship;
    private AcademicTerm $activeTerm;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'Custom Form Scholarship',
            'min_gwa_required' => 2.00,
            'max_renewals' => 4,
            'status' => 'Active',
        ]);

        // Add a required custom text field and a required custom file field
        $this->scholarship->fields()->create([
            'field_label' => 'Declared GWA',
            'field_name' => 'declared_gwa',
            'field_type' => 'number',
            'is_required' => true,
        ]);

        $this->scholarship->fields()->create([
            'field_label' => 'Certificate of Grades (COG)',
            'field_name' => 'certificate_of_grades_cog',
            'field_type' => 'file',
            'is_required' => true,
        ]);

        $this->activeTerm = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function student_submitting_application_must_satisfy_required_custom_fields()
    {
        // 1. Submit without custom fields
        $response = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->scholarship->id,
             ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'custom_fields.declared_gwa',
            'custom_fields.certificate_of_grades_cog'
        ]);

        // 2. Submit with only text field filled, missing file
        $response2 = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->scholarship->id,
                 'custom_fields' => [
                     'declared_gwa' => '1.75'
                 ]
             ]);

        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors([
            'custom_fields.certificate_of_grades_cog'
        ]);
    }

    #[Test]
    public function student_submitting_valid_custom_fields_succeeds_and_uploads_custom_file()
    {
        $response = $this->actingAs($this->student)
             ->postJson(route('student.store'), [
                 'scholarship_id' => $this->scholarship->id,
                 'custom_fields' => [
                     'declared_gwa' => '1.75',
                     'certificate_of_grades_cog' => UploadedFile::fake()->create('my_cog.pdf', 100, 'application/pdf')
                 ]
             ]);

        $response->assertStatus(200);
        $this->assertEquals(1, Application::count());

        $application = Application::first();
        
        // Assert custom fields are stored in the DB
        $this->assertEquals(2, $application->customFields()->count());
        $this->assertEquals('1.75', $application->customFields()->where('field_name', 'Declared GWA')->first()->field_value);
        
        // The uploaded file path should be saved
        $uploadedFilePath = $application->customFields()->where('field_name', 'Certificate of Grades (COG)')->first()->field_value;
        $this->assertNotEmpty($uploadedFilePath);

        // It should also be registered in the documents table for AI scanning
        $this->assertEquals(1, $application->documents()->count());
        $document = $application->documents()->first();
        $this->assertEquals('Certificate of Grades (COG)', $document->document_type);
        $this->assertEquals('my_cog.pdf', $document->original_name);
    }
}
