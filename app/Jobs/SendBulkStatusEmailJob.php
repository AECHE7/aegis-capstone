<?php

namespace App\Jobs;

use App\Models\Application;
use App\Mail\ApplicationStatusMail;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkStatusEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $applicationId;

    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    public function handle()
    {
        $application = Application::with(['user.profile', 'document.aiResult', 'evaluator'])->findOrFail($this->applicationId);
        
        if ($application->user && $application->user->email) {
            $mailSubject = "[A.E.G.I.S.] Official Update: Application " . strtoupper($application->status);
            Mail::to($application->user->email)->send(new ApplicationStatusMail($application));

            // Log email in EmailLog
            EmailLog::create([
                'application_id' => $application->id,
                'recipient' => $application->user->email,
                'subject' => $mailSubject,
                'content' => "Status updated to: {$application->status}. Remarks: " . ($application->remarks ?? 'None')
            ]);
        }
    }
}
