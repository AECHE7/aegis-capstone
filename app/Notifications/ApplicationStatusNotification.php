<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class ApplicationStatusNotification extends Notification
{
    use Queueable;

    protected $application;

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
            'title' => 'Application Status Update',
            'message' => "Your application APP-{$this->application->id} for {$scholarshipName} has been {$this->application->status}.",
            'url' => url('/student/dashboard'),
            'status' => $this->application->status,
            'remarks' => $this->application->remarks,
            'type' => 'status_update'
        ];
    }
}
