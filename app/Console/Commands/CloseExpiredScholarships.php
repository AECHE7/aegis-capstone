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

        // Process scheduled announcements that are now live and have not been notified
        $dueAnnouncements = \App\Models\Announcement::whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('scheduled_delete_at')
                  ->orWhere('scheduled_delete_at', '>', now());
            })
            ->get();

        $notifiedCount = 0;
        foreach ($dueAnnouncements as $announcement) {
            $alreadyNotified = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('data', 'like', '%"announcement_id":' . $announcement->id . '%')
                ->exists();

            if (!$alreadyNotified) {
                $users = \App\Models\User::where('is_active', true)->get();
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NewAnnouncementNotification($announcement));
                $notifiedCount++;
                Log::info("Dispatched scheduled announcement notification: '{$announcement->title}' (ID: {$announcement->id}) to {$users->count()} users.");
            }
        }

        if ($notifiedCount > 0) {
            $this->info("Dispatched notifications for {$notifiedCount} newly active scheduled announcements.");
        }

        $this->info('Expired scholarships and scheduled announcements processing complete.');
        return 0;
    }
}
