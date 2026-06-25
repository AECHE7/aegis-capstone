<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusMail;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $student;
    private $scholarship1;
    private $scholarship2;
    private $app1;
    private $app2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'name' => 'OSA Admin Test',
            'email' => 'admintest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Create Student User
        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);

        // Create Scholarships
        $this->scholarship1 = Scholarship::create([
            'name' => 'DOST-SEI Merit Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        $this->scholarship2 = Scholarship::create([
            'name' => 'College Scholar',
            'min_gwa_required' => 1.75,
            'status' => 'Active'
        ]);

        // Create Applications
        $this->app1 = Application::create([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship1->id,
            'program_name' => $this->scholarship1->name,
            'gwa' => '1.75',
            'status' => 'Pending',
        ]);

        $this->app2 = new Application([
            'user_id' => $this->student->id,
            'scholarship_id' => $this->scholarship2->id,
            'program_name' => $this->scholarship2->name,
            'gwa' => '1.50',
            'status' => 'Approved',
        ]);
        $this->app2->created_at = \Carbon\Carbon::parse('2025-06-01 10:00:00');
        $this->app2->save();

        // Assign admin to both scholarships to allow filtering in report/dashboard test
        $this->admin->scholarships()->sync([$this->scholarship1->id, $this->scholarship2->id]);
    }

    public function test_admin_can_filter_applications_on_dashboard(): void
    {
        // 1. Unfiltered request should return both
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('APP-' . $this->app1->id);
        $response->assertSee('APP-' . $this->app2->id);

        // 2. Filter by scholarship_id
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['scholarship_id' => $this->scholarship1->id]));
        $response->assertStatus(200);
        $response->assertSee('APP-' . $this->app1->id);
        $response->assertDontSee('APP-' . $this->app2->id);

        // 3. Filter by status
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['status' => 'Approved']));
        $response->assertStatus(200);
        $response->assertDontSee('APP-' . $this->app1->id);
        $response->assertSee('APP-' . $this->app2->id);

        // 4. Filter by year
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['year' => '2025']));
        $response->assertStatus(200);
        $response->assertDontSee('APP-' . $this->app1->id);
        $response->assertSee('APP-' . $this->app2->id);
    }

    public function test_admin_can_export_filtered_csv(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.export', ['status' => 'Approved']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('College Scholar', $content);
        $this->assertStringNotContainsString('DOST-SEI Merit Scholarship', $content);
    }

    public function test_admin_can_export_filtered_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.exportPdf', ['status' => 'Pending']));

        $response->assertStatus(200);
        // Barryvdh DomPDF facade returns a download file attachment
        $this->assertStringContainsString('attachment; filename=aegis_official_report_', $response->headers->get('Content-Disposition'));
    }

    public function test_updating_status_dispatches_email_notification(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.updateStatus', $this->app1->id), [
                'status' => 'Approved',
                'remarks' => 'Authentic academic document verified.'
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        
        // Assert email was sent with the PDF attachment
        Mail::assertSent(ApplicationStatusMail::class, function ($mail) {
            $mail->build();
            
            $hasPdfAttachment = false;
            foreach ($mail->rawAttachments as $attachment) {
                if (strpos($attachment['name'], "APP-{$this->app1->id}_Approved_Form.pdf") !== false) {
                    $hasPdfAttachment = true;
                    break;
                }
            }

            return $mail->hasTo($this->student->email) 
                && $mail->application->status === 'Approved'
                && $hasPdfAttachment;
        });
    }

    public function test_student_upload_uses_uuid_anonymized_renaming_and_logs_status(): void
    {
        // Create a fresh student user with no existing applications
        $student = User::create([
            'name' => 'Fresh Student',
            'email' => 'freshstudent@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'email_verified_at' => now()
        ]);

        // 1. Create active academic term
        $activeTerm = \App\Models\AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => true
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('my_transcript.png', 100, 'image/png');

        $response = $this->actingAs($student)
            ->post(route('student.store'), [
                'scholarship_id' => $this->scholarship1->id,
                'gwa' => '1.75',
                'document' => $file
            ]);

        $response->assertRedirect(route('student.dashboard'));

        // Retrieve created application
        $app = Application::where('user_id', $student->id)
            ->where('scholarship_id', $this->scholarship1->id)
            ->first();

        $this->assertNotNull($app);
        $this->assertEquals($activeTerm->id, $app->academic_term_id);

        // Check document renaming
        $doc = $app->document;
        $this->assertNotNull($doc);
        $this->assertEquals('my_transcript.png', $doc->original_name);
        $this->assertEquals('COG', $doc->document_type);
        
        // Assert filename is 64 chars long (SHA-256 hash length) + '.png'
        $filename = basename($doc->file_path);
        $this->assertEquals(64 + 4, strlen($filename)); // 64 hex chars + .png

        // Check status logs
        $logs = $app->statusLogs;
        $this->assertCount(1, $logs);
        $this->assertEquals('Pending', $logs->first()->status);
    }

    public function test_status_and_email_logging_on_admin_review_and_decision(): void
    {
        // Set up initial log
        \App\Models\StatusLog::create([
            'application_id' => $this->app1->id,
            'status' => 'Pending',
            'remarks' => 'Submitted.',
            'changed_by' => $this->student->id
        ]);

        // 1. Admin opens review screen (which moves status to Under Review)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.review', $this->app1->id));

        $response->assertStatus(200);

        // Assert log was created
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $this->app1->id,
            'status' => 'Under Review',
            'changed_by' => $this->admin->id
        ]);

        // 2. Admin submits decision (Approved)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.updateStatus', $this->app1->id), [
                'status' => 'Approved',
                'remarks' => 'Looks authentic.'
            ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Assert final status log
        $this->assertDatabaseHas('status_logs', [
            'application_id' => $this->app1->id,
            'status' => 'Approved',
            'remarks' => 'Looks authentic.',
            'changed_by' => $this->admin->id
        ]);

        // Assert email log was created
        $this->assertDatabaseHas('email_logs', [
            'application_id' => $this->app1->id,
            'recipient' => $this->student->email,
            'subject' => '[A.E.G.I.S.] Official Update: Application APPROVED'
        ]);
    }
}
