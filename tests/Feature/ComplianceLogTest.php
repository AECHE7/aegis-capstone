<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\AdminActionLog;
use App\Models\AuthLog;
use App\Models\ConfigChangeLog;
use App\Models\Document;
use App\Models\EmailLog;
use App\Models\ExportAccessLog;
use App\Models\Scholarship;
use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComplianceLogTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;
    protected $admin;
    protected $student;
    protected $scholarship;
    protected $term;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed settings defaults to avoid null old_value in tests
        \App\Models\Setting::set('app_name', 'A.E.G.I.S.');
        \App\Models\Setting::set('university_name', 'Central Luzon State University');
        \App\Models\Setting::set('ai_fraud_threshold', 50.0);
        \App\Models\Setting::set('gwa_discrepancy_tolerance', 0.01);
        \App\Models\Setting::set('mfa_enforcement', 'all');
        \App\Models\Setting::set('auto_approval_enabled', '0');
        \App\Models\Setting::set('auto_approval_min_confidence', '95.0');
        \App\Models\Setting::set('auto_approval_max_anomalies', '0');

        $this->superadmin = User::create([
            'name' => 'OSA Director',
            'email' => 'director@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'OSA Staff',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'GAD Student Assistance',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
            'max_renewals' => 4,
        ]);

        $this->term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => true,
        ]);
    }

    public function test_auth_logging_on_login_success_and_failure(): void
    {
        // 1. Test Login Failure
        $response = $this->post(route('login.submit'), [
            'email' => 'juan@clsu.edu.ph',
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('auth_logs', [
            'email_attempted' => 'juan@clsu.edu.ph',
            'event_type' => 'login_failed',
            'status' => 'failed',
        ]);

        // 2. Test Login Success (bypass MFA for admin test dummy)
        $response = $this->post(route('login.submit'), [
            'email' => 'director@clsu.edu.ph',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('auth_logs', [
            'email_attempted' => 'director@clsu.edu.ph',
            'event_type' => 'login_success',
            'status' => 'success',
        ]);
    }

    public function test_admin_action_logging_on_scholarship_creation(): void
    {
        $this->actingAs($this->superadmin);

        $response = $this->post(route('superadmin.scholarships.store'), [
            'name' => 'New Test Grant',
            'min_gwa_required' => 1.75,
            'max_renewals' => 2,
        ]);

        $this->assertDatabaseHas('admin_action_logs', [
            'user_id' => $this->superadmin->id,
            'action' => 'scholarship_created',
            'target_type' => 'Scholarship',
        ]);
    }

    public function test_config_change_logging_on_settings_update(): void
    {
        $this->actingAs($this->superadmin);

        $response = $this->post(route('superadmin.settings.update'), [
            'app_name' => 'Updated AEGIS',
            'university_name' => 'CLSU Main',
            'ai_fraud_threshold' => 60.0,
            'gwa_discrepancy_tolerance' => 0.05,
            'mfa_enforcement' => 'students',
            'auto_approval_enabled' => '1',
            'auto_approval_min_confidence' => 90.0,
            'auto_approval_max_anomalies' => 1,
        ]);

        $this->assertDatabaseHas('config_change_logs', [
            'user_id' => $this->superadmin->id,
            'setting_key' => 'app_name',
            'old_value' => 'A.E.G.I.S.',
            'new_value' => 'Updated AEGIS',
        ]);
    }

    public function test_export_access_logging_on_compliance_downloads(): void
    {
        $this->actingAs($this->superadmin);

        // Download CSV
        $response = $this->get(route('superadmin.export.ai-scan.csv'));
        $response->assertStatus(200);

        $this->assertDatabaseHas('export_access_logs', [
            'user_id' => $this->superadmin->id,
            'export_type' => 'ai_scan_log',
            'format' => 'csv',
        ]);

        // Download PDF
        $response = $this->get(route('superadmin.export.ai-scan.pdf'));
        $response->assertStatus(200);

        $this->assertDatabaseHas('export_access_logs', [
            'user_id' => $this->superadmin->id,
            'export_type' => 'ai_scan_log',
            'format' => 'pdf',
        ]);
    }

    public function test_document_upload_logs_record_uploader(): void
    {
        $this->actingAs($this->student);

        // Create application profile to satisfy middleware/controller requirements
        $this->student->profile()->create([
            'clsu_id_number' => '2023-1111',
            'contact_number' => '09171234567',
            'college' => 'CVSM',
            'course' => 'DVM',
            'year_level' => '1st Year',
        ]);

        // Upload document via student.store
        $file = UploadedFile::fake()->create('grades.pdf', 500);

        $response = $this->post(route('student.store'), [
            'scholarship_id' => $this->scholarship->id,
            'document' => $file,
        ]);

        $this->assertDatabaseHas('documents', [
            'upload_event' => 'initial',
            'uploaded_by' => $this->student->id,
        ]);
    }

    public function test_all_log_export_endpoints_return_200(): void
    {
        $this->actingAs($this->superadmin);

        $endpoints = [
            'superadmin.audit.csv', 'superadmin.audit.pdf',
            'superadmin.emaillog.csv', 'superadmin.emaillog.pdf',
            'superadmin.export.ai-scan.csv', 'superadmin.export.ai-scan.pdf',
            'superadmin.export.evaluation.csv', 'superadmin.export.evaluation.pdf',
            'superadmin.export.auth-log.csv', 'superadmin.export.auth-log.pdf',
            'superadmin.export.admin-action.csv', 'superadmin.export.admin-action.pdf',
            'superadmin.export.config-change.csv', 'superadmin.export.config-change.pdf',
            'superadmin.export.scholarship-change.csv', 'superadmin.export.scholarship-change.pdf',
            'superadmin.export.export-access.csv', 'superadmin.export.export-access.pdf',
            'superadmin.export.student-timeline.csv', 'superadmin.export.student-timeline.pdf',
            'superadmin.export.doc-upload.csv', 'superadmin.export.doc-upload.pdf',
        ];

        foreach ($endpoints as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
        }
    }
}
