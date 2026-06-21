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
        // 1. Create User ID 1 (Dummy Student)
        DB::table('users')->insert([
            'name' => 'Juan Dela Cruz',
            'email' => 'student@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create User ID 2 (Dummy OSA Admin)
        DB::table('users')->insert([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Scholarship ID 1
        DB::table('scholarships')->insert([
            'name' => 'CHED Tulong Dunong',
            'funding_agency' => 'CHED',
            'min_gwa' => '2.50',
            'active_period' => '2025-2026',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}