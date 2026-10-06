<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure essential admin users exist (guaranteed credentials, safe to run on boot)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@clsu.edu.ph'],
            [
                'name' => 'OSA Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'staff@clsu.edu.ph'],
            [
                'name' => 'OSA Staff',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'director@clsu.edu.ph'],
            [
                'name' => 'OSA Director',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@clsu.edu.ph'],
            [
                'name' => 'CLSU Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        $demoStudent = \App\Models\User::updateOrCreate(
            ['email' => 'student@clsu.edu.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
                'dpa_consent_at' => now()
            ]
        );

        if (!$demoStudent->profile()->exists()) {
            \App\Models\StudentProfile::create([
                'user_id' => $demoStudent->id,
                'clsu_id_number' => '22-1234',
                'college' => 'College of Science',
                'course' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'contact_number' => '09171234567',
                'guardian_name' => 'Maria Dela Cruz',
                'emergency_contact_number' => '09181234567',
            ]);
        }

        $demoStudentAlias = \App\Models\User::updateOrCreate(
            ['email' => 'student.demo@clsu.edu.ph'],
            [
                'name' => 'Juan Dela Cruz (Demo)',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
                'dpa_consent_at' => now()
            ]
        );

        if (!$demoStudentAlias->profile()->exists()) {
            \App\Models\StudentProfile::create([
                'user_id' => $demoStudentAlias->id,
                'clsu_id_number' => '22-1234',
                'college' => 'College of Science',
                'course' => 'BS Information Technology',
                'year_level' => '3rd Year',
                'contact_number' => '09171234567',
                'guardian_name' => 'Maria Dela Cruz',
                'emergency_contact_number' => '09181234567',
            ]);
        }

        $applyStudent = \App\Models\User::firstOrCreate(
            ['email' => 'student_apply@clsu.edu.ph'],
            [
                'name' => 'Maria Clara Santos',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
                'dpa_consent_at' => now()
            ]
        );

        if (!$applyStudent->profile()->exists()) {
            \App\Models\StudentProfile::create([
                'user_id' => $applyStudent->id,
                'clsu_id_number' => '23-5678',
                'college' => 'College of Agriculture',
                'course' => 'BS Agriculture',
                'year_level' => '2nd Year',
                'contact_number' => '09179876543',
                'guardian_name' => 'Pedro Santos',
                'emergency_contact_number' => '09189876543',
                'province' => 'Nueva Ecija',
                'city_municipality' => 'Science City of Muñoz',
                'barangay' => 'Villa Santos'
            ]);
        }

        \App\Models\User::firstOrCreate(
            ['email' => 'gadianoriel07@gmail.com'],
            [
                'name' => 'Master Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now()
            ]
        );

        // Ensure default settings are populated
        \App\Models\Setting::firstOrCreate(['key' => 'app_name'], ['value' => 'A.E.G.I.S.']);
        \App\Models\Setting::firstOrCreate(['key' => 'university_name'], ['value' => 'Central Luzon State University']);
        \App\Models\Setting::firstOrCreate(['key' => 'ai_fraud_threshold'], ['value' => '70.0']);
        \App\Models\Setting::firstOrCreate(['key' => 'gwa_discrepancy_tolerance'], ['value' => '0.01']);
        \App\Models\Setting::firstOrCreate(['key' => 'app_logo'], ['value' => null]);
        \App\Models\Setting::firstOrCreate(['key' => 'master_email'], ['value' => 'gadianoriel07@gmail.com']);

        // 2. Ensure Official Institutional Scholarships exist
        \App\Models\Scholarship::firstOrCreate(
            ['name' => 'DOST-SEI Merit Scholarship'],
            ['min_gwa_required' => 1.50, 'status' => 'Active', 'max_renewals' => 4]
        );
        \App\Models\Scholarship::firstOrCreate(
            ['name' => 'University Scholar (Institutional)'],
            ['min_gwa_required' => 1.45, 'status' => 'Active', 'max_renewals' => 4]
        );
        \App\Models\Scholarship::firstOrCreate(
            ['name' => 'College Scholar (Institutional)'],
            ['min_gwa_required' => 1.75, 'status' => 'Active', 'max_renewals' => 4]
        );
        \App\Models\Scholarship::firstOrCreate(
            ['name' => 'CHED Tulong Dunong Program'],
            ['min_gwa_required' => 2.50, 'status' => 'Active', 'max_renewals' => 4]
        );

        // 3. Ensure Academic Terms exist
        \App\Models\AcademicTerm::firstOrCreate(
            ['semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['is_active' => false]
        );
        \App\Models\AcademicTerm::firstOrCreate(
            ['semester' => '2nd Semester', 'academic_year' => '2025-2026'],
            ['is_active' => true]
        );
    }
}