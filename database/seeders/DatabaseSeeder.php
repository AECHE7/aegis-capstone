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
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create User ID 2 (Dummy OSA Admin)
        DB::table('users')->insert([
            'name' => 'OSA Admin',
            'email' => 'admin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create User ID 3 (Dummy Super Admin)
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@clsu.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Scholarship ID 1
        DB::table('scholarships')->insert([
            'name' => 'CHED Tulong Dunong',
            'description' => 'CHED Tulong Dunong Scholarship Program',
            'min_gwa_required' => 2.50,
            'deadline' => '2026-12-31',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}