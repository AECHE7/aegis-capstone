<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Application;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BroadcastAnnouncementEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;
    public $content;
    public $target;

    /**
     * Create a new job instance.
     */
    public function __construct(string $title, string $content, string $target)
    {
        $this->title = $title;
        $this->content = $content;
        $this->target = $target;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipients = collect();

        if ($this->target === 'all_students') {
            $recipients = User::where('role', 'student')->get();
        } elseif ($this->target === 'approved_scholars') {
            $userIds = Application::where('status', 'Approved')->pluck('user_id')->unique();
            $recipients = User::whereIn('id', $userIds)->get();
        } elseif (is_numeric($this->target)) {
            $userIds = Application::where('scholarship_id', $this->target)->pluck('user_id')->unique();
            $recipients = User::whereIn('id', $userIds)->get();
        }

        foreach ($recipients as $recipient) {
            try {
                // Send Email
                Mail::to($recipient->email)->send(new AnnouncementMail($this->title, $this->content));

                // Log email in Audit Log table (EmailLog)
                \App\Models\EmailLog::create([
                    'application_id' => null,
                    'recipient' => $recipient->email,
                    'subject' => "[A.E.G.I.S. Broadcast] " . $this->title,
                    'content' => $this->content
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send broadcast email to {$recipient->email}: " . $e->getMessage());
            }
        }
    }
}
