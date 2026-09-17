<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StudentPurgeService;
use App\Models\User;

class PurgeStudentUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aegis:purge-students {--force : Bypass confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge all student users and their associated applications from the database for fresh testing';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = User::where('role', 'student')->count();

        if ($count === 0) {
            $this->info('No student users found in the database.');
            return Command::SUCCESS;
        }

        if (!$this->option('force')) {
            $confirm = $this->confirm("Are you sure you want to delete ALL {$count} student users and their application records?", false);
            if (!$confirm) {
                $this->warn('Operation cancelled.');
                return Command::FAILURE;
            }
        }

        $this->info("Purging {$count} student users and related records...");
        $purged = StudentPurgeService::purgeAllStudents();

        $this->info("Successfully purged {$purged} student user accounts.");
        $this->line("Remaining staff & admin accounts: " . User::count());

        return Command::SUCCESS;
    }
}
