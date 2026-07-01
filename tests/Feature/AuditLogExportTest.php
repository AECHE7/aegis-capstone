<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\StatusLog;
use App\Models\EmailLog;
use App\Models\Scholarship;

class AuditLogExportTest extends TestCase
{
    use RefreshDatabase;

    /** Create a superadmin user for each test */
    private function makeSuperAdmin(): User
    {
        return User::create([
            'name'     => 'Director OSA',
            'email'    => 'superadmin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role'     => 'superadmin',
        ]);
    }

    /** Create a regular admin user to test 403 blocking */
    private function makeAdmin(): User
    {
        return User::create([
            'name'     => 'Admin Staff',
            'email'    => 'admin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    /** Seed a StatusLog entry tied to an application */
    private function seedStatusLog(): void
    {
        $scholarship = Scholarship::create([
            'name'             => 'Test Grant',
            'min_gwa_required' => 2.00,
            'status'           => 'Active',
        ]);

        $student = User::create([
            'name'     => 'Jane Student',
            'email'    => 'jane@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role'     => 'student',
        ]);

        $application = Application::create([
            'user_id'        => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name'   => 'Test Grant',
            'gwa'            => 1.75,
            'status'         => 'Approved',
        ]);

        StatusLog::create([
            'application_id' => $application->id,
            'status'         => 'Approved',
            'remarks'        => 'Meets all requirements.',
            'changed_by'     => $student->id,
        ]);
    }

    /** Seed an EmailLog entry */
    private function seedEmailLog(): void
    {
        $scholarship = Scholarship::create([
            'name'             => 'Email Test Grant',
            'min_gwa_required' => 2.00,
            'status'           => 'Active',
        ]);

        $student = User::create([
            'name'     => 'Email Student',
            'email'    => 'emailstudent@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role'     => 'student',
        ]);

        $application = Application::create([
            'user_id'        => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name'   => 'Email Test Grant',
            'gwa'            => 1.80,
            'status'         => 'Pending',
        ]);

        EmailLog::create([
            'application_id' => $application->id,
            'recipient'      => 'emailstudent@clsu.edu.ph',
            'subject'        => 'Your application has been received.',
            'content'        => 'Dear Student, your application is under review.',
        ]);
    }

    // ── AUDIT CSV TESTS ───────────────────────────────────────────────────────

    /** Superadmin can download the status audit CSV */
    public function test_superadmin_can_download_audit_csv(): void
    {
        $this->seedStatusLog();
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.audit.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Status Changed To', $response->streamedContent());
    }

    /** Audit CSV rows contain expected column headers */
    public function test_audit_csv_contains_correct_headers(): void
    {
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.audit.csv'));
        $content  = $response->streamedContent();

        $this->assertStringContainsString('Log ID',           $content);
        $this->assertStringContainsString('App Ref',          $content);
        $this->assertStringContainsString('Student Name',     $content);
        $this->assertStringContainsString('Status Changed To', $content);
        $this->assertStringContainsString('Changed By',       $content);
        $this->assertStringContainsString('Timestamp',        $content);
    }

    /** Admin role is blocked from audit CSV with 403 */
    public function test_admin_cannot_access_audit_csv(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('superadmin.audit.csv'));
        $response->assertStatus(403);
    }

    // ── AUDIT PDF TESTS ───────────────────────────────────────────────────────

    /** Superadmin can download the status audit PDF */
    public function test_superadmin_can_download_audit_pdf(): void
    {
        $this->seedStatusLog();
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.audit.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** Admin role is blocked from audit PDF with 403 */
    public function test_admin_cannot_access_audit_pdf(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('superadmin.audit.pdf'));
        $response->assertStatus(403);
    }

    // ── EMAIL LOG CSV TESTS ───────────────────────────────────────────────────

    /** Superadmin can download the email dispatch log CSV */
    public function test_superadmin_can_download_email_log_csv(): void
    {
        $this->seedEmailLog();
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.emaillog.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Recipient Email', $response->streamedContent());
    }

    /** Email log CSV rows contain expected column headers */
    public function test_email_log_csv_contains_correct_headers(): void
    {
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.emaillog.csv'));
        $content  = $response->streamedContent();

        $this->assertStringContainsString('Log ID',          $content);
        $this->assertStringContainsString('App Ref',         $content);
        $this->assertStringContainsString('Student Name',    $content);
        $this->assertStringContainsString('Recipient Email', $content);
        $this->assertStringContainsString('Subject',         $content);
        $this->assertStringContainsString('Timestamp',       $content);
    }

    /** Admin role is blocked from email log CSV with 403 */
    public function test_admin_cannot_access_email_log_csv(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('superadmin.emaillog.csv'));
        $response->assertStatus(403);
    }

    // ── EMAIL LOG PDF TESTS ───────────────────────────────────────────────────

    /** Superadmin can download the email log PDF */
    public function test_superadmin_can_download_email_log_pdf(): void
    {
        $this->seedEmailLog();
        $superadmin = $this->makeSuperAdmin();

        $response = $this->actingAs($superadmin)->get(route('superadmin.emaillog.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** Admin role is blocked from email log PDF with 403 */
    public function test_admin_cannot_access_email_log_pdf(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('superadmin.emaillog.pdf'));
        $response->assertStatus(403);
    }

    // ── DATE RANGE FILTER TESTS ───────────────────────────────────────────────

    /** Date range filter scopes audit CSV rows correctly */
    public function test_audit_csv_date_filter_scopes_results(): void
    {
        $this->seedStatusLog();
        $superadmin = $this->makeSuperAdmin();

        // Request with a future date range — should return headers only (no rows)
        $futureFrom = now()->addYears(10)->format('Y-m-d');
        $response = $this->actingAs($superadmin)->get(route('superadmin.audit.csv', [
            'date_from' => $futureFrom,
            'date_to'   => $futureFrom,
        ]));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        // Headers always present, but no data rows from a far-future date range
        $this->assertStringContainsString('Status Changed To', $content);
        $this->assertStringNotContainsString('Approved', $content); // seeded row should be excluded
    }
}
