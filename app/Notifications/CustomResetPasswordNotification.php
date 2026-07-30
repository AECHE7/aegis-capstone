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
     * Resolve target public domain base URL.
     */
    protected function getAppDomain(): string
    {
        $domain = config('app.url');
        if (!$domain || str_contains($domain, 'localhost')) {
            if (request()->hasHeader('X-Forwarded-Host')) {
                $proto = request()->header('X-Forwarded-Proto', 'https');
                $host = request()->header('X-Forwarded-Host');
                $domain = "{$proto}://{$host}";
            } elseif (request()->getHost() && !str_contains(request()->getHost(), 'localhost')) {
                $domain = request()->schemeAndHttpHost();
            } else {
                $domain = 'https://aegis-capstone.onrender.com';
            }
        }
        return rtrim($domain, '/');
    }

    /**
     * Build the mail representation of the notification using custom CLSU HTML email template.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $relativeUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false);

        $resetUrl = $this->getAppDomain() . $relativeUrl;

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
            ->view('emails.reset_password', [
                'name' => $notifiable->name,
                'resetUrl' => $resetUrl,
            ]);
    }
}
