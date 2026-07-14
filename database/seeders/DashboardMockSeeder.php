<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\AuthLog;
use App\Models\AdminActionLog;
use App\Models\ConfigChangeLog;
use App\Models\ExportAccessLog;
use App\Models\EmailLog;
use App\Models\StatusLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardMockSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Wipe all existing transactional and log data in child-to-parent order to avoid foreign key issues
        AIResult::query()->delete();
        Document::query()->delete();
        StatusLog::query()->delete();
        EmailLog::query()->delete();
        AuthLog::query()->delete();
        AdminActionLog::query()->delete();
        ConfigChangeLog::query()->delete();
        ExportAccessLog::query()->delete();
        Application::withTrashed()->forceDelete();
        StudentProfile::query()->delete();
        User::withTrashed()->where('role', 'student')->forceDelete();


        // 2. Ensure Admin and Superadmin accounts exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@clsu.edu.ph'],
            [
                'name' => 'OSA Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true
            ]
        );

        $director = User::firstOrCreate(
            ['email' => 'director@clsu.edu.ph'],
            [
                'name' => 'OSA Director',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
                'is_active' => true
            ]
        );

        // 3. Ensure Academic Terms exist
        $pastTerm = AcademicTerm::firstOrCreate(
            ['semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['is_active' => false]
        );

        $activeTerm = AcademicTerm::firstOrCreate(
            ['semester' => '2nd Semester', 'academic_year' => '2025-2026'],
            ['is_active' => true]
        );

        // 4. Ensure Scholarships exist
        $scholarships = [
            Scholarship::firstOrCreate(['name' => 'DOST-SEI Merit Scholarship'], ['min_gwa_required' => 1.50, 'status' => 'Active', 'max_renewals' => 4]),
            Scholarship::firstOrCreate(['name' => 'University Scholar (Institutional)'], ['min_gwa_required' => 1.45, 'status' => 'Active', 'max_renewals' => 4]),
            Scholarship::firstOrCreate(['name' => 'College Scholar (Institutional)'], ['min_gwa_required' => 1.75, 'status' => 'Active', 'max_renewals' => 4]),
            Scholarship::firstOrCreate(['name' => 'CHED Tulong Dunong Program'], ['min_gwa_required' => 2.50, 'status' => 'Active', 'max_renewals' => 4])
        ];

        // 5. Default Settings
        \App\Models\Setting::set('app_name', 'A.E.G.I.S.');
        \App\Models\Setting::set('university_name', 'Central Luzon State University');
        \App\Models\Setting::set('ai_fraud_threshold', '50.0');
        \App\Models\Setting::set('gwa_discrepancy_tolerance', '0.01');
        \App\Models\Setting::set('mfa_enforcement', 'all');
        \App\Models\Setting::set('auto_approval_enabled', '0');
        \App\Models\Setting::set('auto_approval_min_confidence', '95.0');
        \App\Models\Setting::set('auto_approval_max_anomalies', '0');

        // 6. Generate 40 Mock Students with Profiles and Applications
        $colleges = ['CVSM', 'CEA', 'CAS', 'CBAA', 'CAG', 'COEd', 'CF', 'CHed'];
        $courses = [
            'CVSM' => ['DVM'],
            'CEA' => ['BSAE', 'BSCE', 'BSEE'],
            'CAS' => ['BSBio', 'BSChem', 'BSPsych'],
            'CBAA' => ['BSBA', 'BSA', 'BSEntrep'],
            'CAG' => ['BSAgronomy', 'BSHorti'],
            'COEd' => ['BSEd', 'BEEd'],
            'CF' => ['BSFisheries'],
            'CHed' => ['BSFT', 'BSHRM']
        ];
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        $firstNames = ['Juan', 'Maria', 'Jose', 'Angelo', 'Ramon', 'Grace', 'Fe', 'Lito', 'Ana', 'Pedro', 'Rosa', 'Carlo', 'Danilo', 'Elisa', 'Fidel', 'Gloria', 'Helen', 'Irene', 'Jaime', 'Liza'];
        $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Aquino', 'Garcia', 'Mendoza', 'Cruz', 'Torres', 'Diaz', 'Bautista', 'Ramos', 'Gonzales', 'Villanueva', 'Castro', 'Flores'];

        for ($i = 0; $i < 40; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName . ' ' . $lastName . ' ' . rand(10, 99);
            $email = strtolower(str_replace(' ', '', $firstName . $lastName)) . $i . '@clsu2.edu.ph';

            $student = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now()->subDays(rand(10, 50)),
                'is_active' => true
            ]);

            $college = $colleges[array_rand($colleges)];
            $courseList = $courses[$college];
            $course = $courseList[array_rand($courseList)];

            $profile = StudentProfile::create([
                'user_id' => $student->id,
                'clsu_id_number' => sprintf("202%d-%04d", rand(2, 5), rand(1, 9999)),
                'contact_number' => sprintf("0917%07d", rand(1000000, 9999999)),
                'college' => $college,
                'course' => $course,
                'year_level' => $yearLevels[array_rand($yearLevels)],
            ]);

            // Create 1 or 2 Applications
            $numApps = rand(1, 2);
            for ($j = 0; $j < $numApps; $j++) {
                $term = ($j === 0) ? $activeTerm : $pastTerm;
                $scholarship = $scholarships[array_rand($scholarships)];

                // GWA random distribution
                $gwaType = rand(1, 10);
                if ($gwaType <= 3) {
                    $gwa = rand(100, 125) / 100; // Excellent
                } elseif ($gwaType <= 6) {
                    $gwa = rand(126, 150) / 100; // Very Good
                } elseif ($gwaType <= 8) {
                    $gwa = rand(151, 175) / 100; // Good
                } else {
                    $gwa = rand(176, 250) / 100; // Satisfactory / lower
                }

                // Status distribution
                if ($term->is_active) {
                    $statusOptions = ['Pending', 'Under Review', 'Approved', 'Rejected'];
                    $status = $statusOptions[array_rand($statusOptions)];
                } else {
                    $status = rand(1, 10) <= 8 ? 'Approved' : 'Rejected'; // past terms mostly resolved
                }

                $app = Application::create([
                    'user_id' => $student->id,
                    'scholarship_id' => $scholarship->id,
                    'academic_term_id' => $term->id,
                    'program_name' => $scholarship->name,
                    'gwa' => $gwa,
                    'status' => $status,
                    'remarks' => $status === 'Approved' ? 'Met academic performance requirements.' : ($status === 'Rejected' ? 'GWA did not meet program standard.' : null),
                    'evaluated_by' => in_array($status, ['Approved', 'Rejected']) ? (rand(1, 2) === 1 ? $admin->id : $director->id) : null
                ]);

                // Create Document
                $doc = Document::create([
                    'application_id' => $app->id,
                    'file_path' => 'uploads/cog_' . Str::random(10) . '.pdf',
                    'original_name' => 'COG_Sem' . ($term->id) . '_' . str_replace(' ', '_', $student->name) . '.pdf',
                    'document_type' => 'COG',
                    'upload_event' => 'initial',
                    'uploaded_by' => $student->id
                ]);

                // Create AI Scan Results
                $isFraud = rand(1, 10) >= 9; // 10% fraud rate to show on charts
                $fraudProbability = $isFraud ? rand(55, 98) : rand(1, 25);
                $classification = $fraudProbability >= 50 ? 'tampered' : 'authentic';

                $anomalies = [];
                if ($isFraud) {
                    $anomalyPool = ['font_inconsistency', 'metadata_manipulation', 'qr_code_mismatch', 'gwa_discrepancy', 'altered_text'];
                    $numAnomalies = rand(1, 3);
                    shuffle($anomalyPool);
                    $anomalies = array_slice($anomalyPool, 0, $numAnomalies);
                }

                AIResult::create([
                    'document_id' => $doc->id,
                    'classification' => $classification,
                    'fraud_probability' => $fraudProbability,
                    'anomaly_indicators' => $anomalies,
                ]);

                // Create Status Logs
                StatusLog::create([
                    'application_id' => $app->id,
                    'status' => 'Pending',
                    'remarks' => 'Application submitted and entered the verification pipeline.',
                    'changed_by' => $student->id,
                    'created_at' => $app->created_at->subDays(rand(3, 7))
                ]);

                if ($status !== 'Pending') {
                    StatusLog::create([
                        'application_id' => $app->id,
                        'status' => 'Under Review',
                        'remarks' => 'Grade transcripts are under verification.',
                        'changed_by' => $admin->id,
                        'created_at' => $app->created_at->subDays(rand(1, 2))
                    ]);
                }

                if (in_array($status, ['Approved', 'Rejected'])) {
                    StatusLog::create([
                        'application_id' => $app->id,
                        'status' => $status,
                        'remarks' => $app->remarks ?: 'Evaluation finalized.',
                        'changed_by' => $app->evaluated_by,
                        'created_at' => $app->created_at
                    ]);

                    // Create Email Log
                    EmailLog::create([
                        'application_id' => $app->id,
                        'recipient' => $student->email,
                        'subject' => "[A.E.G.I.S.] Official Update: Application " . strtoupper($status),
                        'content' => "Status updated to: {$status}. Remarks: " . ($app->remarks ?? 'None'),
                        'status' => rand(1, 20) === 20 ? 'failed' : 'sent',
                        'error_message' => rand(1, 20) === 20 ? 'SMTP Connection Timed Out' : null
                    ]);
                }
            }

            // 7. Write Auth Logs (Access History)
            $authEventTypes = ['login_success', 'login_failed', 'logout', 'mfa_verified', 'mfa_failed', 'device_trusted'];
            $numAuthLogs = rand(2, 5);
            for ($k = 0; $k < $numAuthLogs; $k++) {
                $ev = $authEventTypes[array_rand($authEventTypes)];
                AuthLog::create([
                    'user_id' => $student->id,
                    'email_attempted' => $student->email,
                    'event_type' => $ev,
                    'ip_address' => sprintf("10.24.%d.%d", rand(10, 250), rand(10, 250)),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
                    'status' => in_array($ev, ['login_failed', 'mfa_failed']) ? 'failed' : 'success',
                    'created_at' => now()->subDays(rand(1, 20))->subHours(rand(1, 23))
                ]);
            }
        }

        // 8. Write Admin action logs
        $adminActions = [
            ['action' => 'scholarship_created', 'target_type' => 'Scholarship', 'desc' => 'Created scholarship program: GAD Student Assistance'],
            ['action' => 'scholarship_updated', 'target_type' => 'Scholarship', 'desc' => 'Updated scholarship program: CHED Tulong Dunong Program'],
            ['action' => 'staff_invited', 'target_type' => 'User', 'desc' => 'Invited staff member: Jose Rizal (jose.rizal@clsu.edu.ph)'],
            ['action' => 'staff_assignments_updated', 'target_type' => 'User', 'desc' => 'Updated scholarship program assignments for Jose Rizal'],
            ['action' => 'mfa_revoked_all', 'target_type' => 'System', 'desc' => 'Revoked all trusted devices system-wide'],
        ];

        foreach ($adminActions as $i => $act) {
            AdminActionLog::create([
                'user_id' => $director->id,
                'action' => $act['action'],
                'target_type' => $act['target_type'],
                'target_id' => rand(1, 5),
                'description' => $act['desc'],
                'ip_address' => '10.24.132.49',
                'created_at' => now()->subDays($i + 1)
            ]);
        }

        // 9. Write Config changes
        $configKeys = [
            ['key' => 'ai_fraud_threshold', 'old' => '45.0', 'new' => '50.0'],
            ['key' => 'gwa_discrepancy_tolerance', 'old' => '0.02', 'new' => '0.01'],
            ['key' => 'mfa_enforcement', 'old' => 'students', 'new' => 'all'],
            ['key' => 'auto_approval_enabled', 'old' => '0', 'new' => '1']
        ];

        foreach ($configKeys as $i => $conf) {
            ConfigChangeLog::create([
                'user_id' => $director->id,
                'setting_key' => $conf['key'],
                'old_value' => $conf['old'],
                'new_value' => $conf['new'],
                'ip_address' => '10.24.132.49',
                'created_at' => now()->subDays($i + 2)
            ]);
        }

        // 10. Write Export Access history
        $exportTypes = ['audit_log', 'email_log', 'ai_scan_log', 'auth_log', 'admin_action_log', 'evaluation_log', 'config_change_log', 'scholarship_change_log', 'student_timeline_log', 'doc_upload_log'];
        foreach ($exportTypes as $i => $type) {
            ExportAccessLog::create([
                'user_id' => $director->id,
                'export_type' => $type,
                'date_from' => now()->subDays(30),
                'date_to' => now(),
                'format' => $i % 2 === 0 ? 'csv' : 'pdf',
                'ip_address' => '10.24.132.49',
                'created_at' => now()->subDays($i + 1)
            ]);
        }

        $this->command->info('A.E.G.I.S. Director analytical dashboard seeder run successfully with 40 students and complete audit logs!');
    }
}
