<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scholarship;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CloseExpiredScholarships extends Command
{
    protected $signature = 'scholarships:close-expired';
    protected $description = 'Automatically close expired scholarships and archive their pending/under-review applications';

    public function handle()
    {
        $today = Carbon::today();
        
        $expiredScholarships = Scholarship::where('status', 'Active')
            ->whereNotNull('deadline')
            ->where('deadline', '<', $today)
            ->get();

        if ($expiredScholarships->isEmpty()) {
            $this->info('No expired scholarships found.');
            return 0;
        }

        foreach ($expiredScholarships as $scholarship) {
            $scholarship->update(['status' => 'Closed']);
            $this->info("Closed scholarship: {$scholarship->name}");
            Log::info("Auto-closed scholarship: {$scholarship->name} (Deadline was {$scholarship->deadline})");

            $affectedApps = Application::where('scholarship_id', $scholarship->id)
                ->whereIn('status', ['Pending', 'Under Review'])
                ->update(['is_archived' => true]);

            if ($affectedApps > 0) {
                $this->info("Archived {$affectedApps} pending/under-review applications for {$scholarship->name}.");
                Log::info("Auto-archived {$affectedApps} pending/under-review applications for scholarship {$scholarship->name} due to expiration.");
            }
        }

        $this->info('Expired scholarships processing complete.');
        return 0;
    }
}
