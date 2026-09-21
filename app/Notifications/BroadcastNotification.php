<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BroadcastNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $content;
    public ?string $url;

    /**
     * Create a new broadcast notification instance.
     */
    public function __construct(string $title, string $content, ?string $url = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->url = $url;
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
        $targetUrl = $this->url;

        if (!$targetUrl) {
            $targetUrl = ($notifiable->role === 'student')
                ? route('student.dashboard')
                : route('admin.dashboard');
        }

        return [
            'title' => 'Broadcast: ' . $this->title,
            'message' => Str::limit(strip_tags($this->content), 120),
            'url' => $targetUrl,
            'type' => 'broadcast',
            'full_content' => $this->content,
        ];
    }
}
