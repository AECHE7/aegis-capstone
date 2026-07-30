<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class CustomVerifyEmailNotification extends VerifyEmail implements ShouldQueue
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
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $domain = $this->getAppDomain();
        $parsed = parse_url($verificationUrl);
        if (isset($parsed['path'])) {
            $pathAndQuery = $parsed['path'] . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            $verificationUrl = $domain . $pathAndQuery;
        }

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
