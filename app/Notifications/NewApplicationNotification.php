<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class NewApplicationNotification extends Notification
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
        $studentName = $this->application->user->name ?? 'A student';
        
        return [
            'application_id' => $this->application->id,
            'title' => 'New Application Submitted',
            'message' => "A new application APP-{$this->application->id} has been submitted for {$scholarshipName} by {$studentName}.",
            'student_name' => $studentName,
            'scholarship_name' => $scholarshipName,
            'type' => 'new_application'
        ];
    }
}
