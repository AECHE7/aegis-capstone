<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StaffInvitationNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $activationUrl = url('/activate-account?token=' . $this->token);

        // Audit Trail: Log email in EmailLog
        try {
            \App\Models\EmailLog::create([
                'application_id' => null,
                'recipient' => $notifiable->email,
                'subject' => '[A.E.G.I.S.] Staff Account Invitation',
                'content' => "Staff invitation link sent. Link: {$activationUrl}"
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log staff invitation email: ' . $e->getMessage());
        }

        return (new MailMessage)
            ->subject('[A.E.G.I.S.] Staff Account Invitation')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('You have been invited by the Super Admin to join the CLSU A.E.G.I.S. Portal as an OSA Staff member.')
            ->line('Please click the button below to set up your password and activate your account.')
            ->action('Activate Account', $activationUrl)
            ->line('This invitation link will expire in 3 days.')
            ->line('If you did not expect this invitation, no further action is required.')
            ->salutation('Best regards, CLSU Office of Student Affairs');
    }
}
