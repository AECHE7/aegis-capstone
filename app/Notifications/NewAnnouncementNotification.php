<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Announcement;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
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
        $targetUrl = ($notifiable->role === 'student')
            ? route('student.announcements')
            : route('admin.announcements.index');

        return [
            'announcement_id' => $this->announcement->id,
            'title' => 'Official Announcement',
            'message' => $this->announcement->title,
            'url' => $targetUrl,
            'type' => 'announcement'
        ];
    }
}
