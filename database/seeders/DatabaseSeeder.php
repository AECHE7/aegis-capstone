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
        // 1. Ensure essential admin users exist (idempotent, safe to run on boot)
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@clsu.edu.ph'],
            [
                'name' => 'OSA Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'director@clsu.edu.ph'],
            [
                'name' => 'OSA Director',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now()
            ]
        );

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