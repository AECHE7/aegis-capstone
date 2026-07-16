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
use Illuminate\Support\Facades\Hash;

class UatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Delete all student-specific records to clear existing data completely
        AIResult::query()->delete();
        Document::query()->delete();
        \App\Models\StatusLog::query()->delete();
        \App\Models\EmailLog::query()->delete();
        \App\Models\ApplicationField::query()->delete();
        StudentProfile::query()->delete();
        Application::withTrashed()->forceDelete();
        User::withTrashed()->where('role', 'student')->forceDelete();

        // 2. Ensure Admin & Super Admin/Director exist and are verified
        User::firstOrCreate(
            ['email' => 'admin@clsu.edu.ph'],
            [
                'name' => 'OSA Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]
        );

        User::firstOrCreate(
            ['email' => 'director@clsu.edu.ph'],
            [
                'name' => 'OSA Director',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now()
            ]
        );

        User::firstOrCreate(
            ['email' => 'gadianoriel07@gmail.com'],
            [
                'name' => 'Master Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now()
            ]
        );

        // 4. Ensure Scholarships exist (re-seed if missing)
        if (Scholarship::count() === 0) {
            Scholarship::create(['name' => 'DOST-SEI Merit Scholarship', 'min_gwa_required' => 1.50, 'status' => 'Active']);
            Scholarship::create(['name' => 'University Scholar (Institutional)', 'min_gwa_required' => 1.45, 'status' => 'Active']);
            Scholarship::create(['name' => 'College Scholar (Institutional)', 'min_gwa_required' => 1.75, 'status' => 'Active']);
            Scholarship::create(['name' => 'CHED Tulong Dunong Program', 'min_gwa_required' => 2.50, 'status' => 'Active']);
        }

        // 5. Ensure Academic Terms exist (re-seed if missing)
        if (AcademicTerm::count() === 0) {
            AcademicTerm::create([
                'semester' => '1st Semester',
                'academic_year' => '2025-2026',
                'is_active' => false
            ]);
            AcademicTerm::create([
                'semester' => '2nd Semester',
                'academic_year' => '2025-2026',
                'is_active' => true
            ]);
        }

        // 6. Ensure Default Settings exist
        \App\Models\Setting::firstOrCreate(['key' => 'app_name'], ['value' => 'A.E.G.I.S.']);
        \App\Models\Setting::firstOrCreate(['key' => 'university_name'], ['value' => 'Central Luzon State University']);
        \App\Models\Setting::firstOrCreate(['key' => 'ai_fraud_threshold'], ['value' => '50.0']);
        \App\Models\Setting::firstOrCreate(['key' => 'gwa_discrepancy_tolerance'], ['value' => '0.01']);
        \App\Models\Setting::firstOrCreate(['key' => 'app_logo'], ['value' => null]);
        \App\Models\Setting::firstOrCreate(['key' => 'master_email'], ['value' => 'gadianoriel07@gmail.com']);

        $this->command->info('Database cleaned! Ready for manual student registration.');
    }
}