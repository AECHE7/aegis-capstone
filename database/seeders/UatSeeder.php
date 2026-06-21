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

        // 5. Generate 30 fake applications linked to the real scholarships
        $statuses = ['Pending', 'Approved', 'Rejected'];

        for ($i = 1; $i <= 30; $i++) {
            $isForged = rand(1, 100) > 60; // 40% chance of being forged
            $status = $statuses[array_rand($statuses)];
            $selectedScholarship = $scholarships[array_rand($scholarships)];

            $app = Application::create([
                'user_id' => $student->id,
                'scholarship_id' => $selectedScholarship->id,
                'program_name' => $selectedScholarship->name,
                'gwa' => number_format(rand(100, 250) / 100, 2),
                'status' => $status,
                'created_at' => now()->subDays(rand(1, 30))
            ]);

            $doc = Document::create([
                'application_id' => $app->id,
                'file_path' => 'mock/path/doc.jpg',
                'original_name' => 'certificate_of_grades.jpg'
            ]);

            AIResult::create([
                'document_id' => $doc->id,
                'fraud_probability' => $isForged ? rand(70, 99) : rand(1, 30),
                'classification' => $isForged ? 'tampered' : 'authentic',
                'heatmap_path' => 'mock/path/heatmap.jpg'
            ]);
        }

        $this->command->info('UAT Batch generation complete! Fully normalized ERD active.');
    }
}