<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmailNotification extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // Audit Trail: Log email in EmailLog
        try {
            \App\Models\EmailLog::create([
                'application_id' => null,
                'recipient' => $notifiable->email,
                'subject' => '[A.E.G.I.S.] Please Verify Your Email Address',
                'content' => "Verification link sent to student registration. Link: {$verificationUrl}"
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log verification email: ' . $e->getMessage());
        }

        return (new MailMessage)
            ->subject('[A.E.G.I.S.] Please Verify Your Email Address')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Thank you for registering a student account at the CLSU A.E.G.I.S. Portal.')
            ->line('Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create this account, no further action is required.')
            ->salutation('Best regards, CLSU Office of Student Affairs');
    }
}
