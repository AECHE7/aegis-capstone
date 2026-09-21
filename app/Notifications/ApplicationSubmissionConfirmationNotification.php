<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class ApplicationSubmissionConfirmationNotification extends Notification
{
    use Queueable;

    protected Application $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $scholarshipName = $this->application->scholarship->name ?? $this->application->program_name;

        return [
            'application_id' => $this->application->id,
            'title' => 'Application Submitted Successfully',
            'message' => "Your application APP-{$this->application->id} for {$scholarshipName} has been received by OSA and is queued for verification.",
            'url' => route('student.dashboard'),
            'status' => $this->application->status,
            'type' => 'submission_confirmation',
        ];
    }
}
