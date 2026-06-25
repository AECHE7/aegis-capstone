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
        // Prevent duplicate seeding on container restarts
        if (DB::table('users')->count() > 0) {
            return;
        }

        // Invoke the UAT seeder to populate all application records, timeline logs, and users
        $this->call(UatSeeder::class);
    }
}