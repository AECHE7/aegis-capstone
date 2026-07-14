<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class CustomResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Audit Trail: Log email in EmailLog
        try {
            \App\Models\EmailLog::create([
                'application_id' => null,
                'recipient' => $notifiable->email,
                'subject' => '[A.E.G.I.S.] Reset Your Password',
                'content' => "Password reset link sent. Link: {$resetUrl}"
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log password reset email: ' . $e->getMessage());
        }

        return (new MailMessage)
            ->subject('[A.E.G.I.S.] Reset Your Password')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Best regards, CLSU Office of Student Affairs');
    }
}
