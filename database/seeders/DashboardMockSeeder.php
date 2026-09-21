<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DashboardMockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Deactivated: All synthetic/mock data generation has been removed from AEGIS.
     * Dashboards and analytics strictly compute from authentic system records.
     */
    public function run(): void
    {
        $this->command?->info('DashboardMockSeeder is deactivated. AEGIS relies exclusively on authentic system data.');
    }
}
