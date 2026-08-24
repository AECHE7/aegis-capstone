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

        // 2. Only invoke UatSeeder (which wipes/resets student data) if no students are registered yet
        $hasStudents = \App\Models\User::where('role', 'student')->exists();
        if (!$hasStudents) {
            $this->call(UatSeeder::class);
        }
    }
}