<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Scholarship;

class ScholarshipSlotsOpenedNotification extends Notification
{
    use Queueable;

    protected $scholarship;

    /**
     * Create a new notification instance.
     */
    public function __construct(Scholarship $scholarship)
    {
        $this->scholarship = $scholarship;
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
        $slots = $this->scholarship->availableSlots();
        $slotText = $slots !== null ? "{$slots} slot(s) currently open." : "Application slots are currently open.";
        $deadlineText = $this->scholarship->deadline ? " Deadline: " . $this->scholarship->deadline->format('M d, Y') . "." : "";

        return [
            'scholarship_id' => $this->scholarship->id,
            'title' => 'Slots Open: ' . $this->scholarship->name,
            'message' => "New scholarship slots are now available for {$this->scholarship->name}! {$slotText}{$deadlineText}",
            'url' => route('student.apply') . '?scholarship_id=' . $this->scholarship->id,
            'type' => 'scholarship_slots'
        ];
    }
}
