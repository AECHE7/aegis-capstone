<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\Scholarship;
use Illuminate\Support\Facades\Hash;

class UatSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure mock directories exist and copy sample images to prevent 404s
        $mockDocDir = storage_path('app/mock/path');
        if (!file_exists($mockDocDir)) {
            mkdir($mockDocDir, 0755, true);
        }
        $mockHeatmapDir = base_path('aegis-ai/mock/path');
        if (!file_exists($mockHeatmapDir)) {
            mkdir($mockHeatmapDir, 0755, true);
        }

        $srcOriginal = base_path('aegis-ai/real_test.jpg');
        if (file_exists($srcOriginal)) {
            copy($srcOriginal, $mockDocDir . '/doc.jpg');
            copy($srcOriginal, $mockHeatmapDir . '/heatmap.jpg');
        }

        // 1. Create a generic student user
        $student = User::firstOrCreate(
            ['email' => 'student@clsu.edu.ph'],
            ['name' => 'Juan Dela Cruz', 'password' => Hash::make('password'), 'role' => 'student']
        );

        // 2. Create their Student Profile
        StudentProfile::firstOrCreate(
            ['user_id' => $student->id],
            [
                'clsu_id_number' => '2023-4567',
                'college' => 'College of Science',
                'course' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'contact_number' => '09123456789'
            ]
        );

        // 3. Create Admin & Super Admin
        User::firstOrCreate(['email' => 'admin@clsu.edu.ph'], ['name' => 'OSA Admin', 'password' => Hash::make('password'), 'role' => 'admin']);
        User::firstOrCreate(['email' => 'director@clsu.edu.ph'], ['name' => 'OSA Director', 'password' => Hash::make('password'), 'role' => 'superadmin']);

        // 4. Create ACTUAL Scholarships! (Fully Normalized)
        $scholarships = [
            Scholarship::create(['name' => 'DOST-SEI Merit Scholarship', 'min_gwa_required' => 1.50, 'status' => 'Active']),
            Scholarship::create(['name' => 'University Scholar (Institutional)', 'min_gwa_required' => 1.45, 'status' => 'Active']),
            Scholarship::create(['name' => 'College Scholar (Institutional)', 'min_gwa_required' => 1.75, 'status' => 'Active']),
            Scholarship::create(['name' => 'CHED Tulong Dunong Program', 'min_gwa_required' => 2.50, 'status' => 'Active']),
        ];

        // 4b. Seed Academic Terms
        $term1 = \App\Models\AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
            'is_active' => false
        ]);
        $term2 = \App\Models\AcademicTerm::create([
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'is_active' => true
        ]);
        $terms = [$term1, $term2];

        // 5. Generate 30 fake applications linked to the real scholarships
        $statuses = ['Pending', 'Approved', 'Rejected'];

        for ($i = 1; $i <= 30; $i++) {
            $isForged = rand(1, 100) > 60; // 40% chance of being forged
            $status = $statuses[array_rand($statuses)];
            $selectedScholarship = $scholarships[array_rand($scholarships)];
            $selectedTerm = $terms[array_rand($terms)];
            $appDate = now()->subDays(rand(1, 30));

            $app = Application::create([
                'user_id' => $student->id,
                'scholarship_id' => $selectedScholarship->id,
                'academic_term_id' => $selectedTerm->id,
                'program_name' => $selectedScholarship->name,
                'gwa' => number_format(rand(100, 250) / 100, 2),
                'status' => $status,
                'created_at' => $appDate,
                'updated_at' => $appDate
            ]);

            $doc = Document::create([
                'application_id' => $app->id,
                'file_path' => 'mock/path/doc.jpg',
                'original_name' => 'certificate_of_grades.jpg',
                'document_type' => 'COG'
            ]);

            AIResult::create([
                'document_id' => $doc->id,
                'fraud_probability' => $isForged ? rand(70, 99) : rand(1, 30),
                'classification' => $isForged ? 'tampered' : 'authentic',
                'heatmap_path' => 'mock/path/heatmap.jpg'
            ]);

            // Seed status and email logs audit history
            \App\Models\StatusLog::create([
                'application_id' => $app->id,
                'status' => 'Pending',
                'remarks' => 'Application submitted and entered the verification pipeline.',
                'changed_by' => $student->id,
                'created_at' => $appDate,
                'updated_at' => $appDate
            ]);

            if ($status === 'Under Review' || $status === 'Approved' || $status === 'Rejected') {
                $reviewDate = $appDate->copy()->addHours(rand(1, 24));
                \App\Models\StatusLog::create([
                    'application_id' => $app->id,
                    'status' => 'Under Review',
                    'remarks' => 'Application opened for verification review.',
                    'changed_by' => 2, // Admin user ID
                    'created_at' => $reviewDate,
                    'updated_at' => $reviewDate
                ]);

                if ($status === 'Approved' || $status === 'Rejected') {
                    $decisionDate = $reviewDate->copy()->addHours(rand(1, 12));
                    $remarks = $status === 'Approved' 
                        ? 'Academic document verified. Approved.' 
                        : 'Grade requirements not met or forensic scan flagged document.';

                    \App\Models\StatusLog::create([
                        'application_id' => $app->id,
                        'status' => $status,
                        'remarks' => $remarks,
                        'changed_by' => 2,
                        'created_at' => $decisionDate,
                        'updated_at' => $decisionDate
                    ]);

                    \App\Models\EmailLog::create([
                        'application_id' => $app->id,
                        'recipient' => $student->email,
                        'subject' => "[A.E.G.I.S.] Official Update: Application " . strtoupper($status),
                        'content' => "Status updated to: {$status}. Remarks: {$remarks}",
                        'created_at' => $decisionDate,
                        'updated_at' => $decisionDate
                    ]);
                }
            }
        }

        $this->command->info('UAT Batch generation complete! Fully normalized ERD active.');
    }
}