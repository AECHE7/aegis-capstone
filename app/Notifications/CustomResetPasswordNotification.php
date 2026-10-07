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
        // 1. If an active web request exists, prioritize the incoming client host (e.g., aegis-capstone.onrender.com)
        if (app()->bound('request') && request()) {
            if (request()->hasHeader('X-Forwarded-Host')) {
                $proto = request()->header('X-Forwarded-Proto', 'https');
                $host  = request()->header('X-Forwarded-Host');
                if ($host && !in_array($host, ['localhost', '127.0.0.1'], true) && !str_contains($host, 'clsu.osa.scholarship')) {
                    return rtrim("{$proto}://{$host}", '/');
                }
            }
            $host = request()->getHost();
            if ($host && !in_array($host, ['localhost', '127.0.0.1'], true) && !str_contains($host, 'clsu.osa.scholarship')) {
                return rtrim(request()->schemeAndHttpHost(), '/');
            }
        }

        // 2. Render cloud deployment environment URL
        if (env('RENDER_EXTERNAL_URL')) {
            return rtrim(env('RENDER_EXTERNAL_URL'), '/');
        }

        // 3. Configured app.url (ignoring mock or localhost domains)
        $domain = config('app.url');
        if ($domain && !str_contains($domain, 'localhost') && !str_contains($domain, 'clsu.osa.scholarship')) {
            return rtrim($domain, '/');
        }

        // 4. Fallback to production Render URL
        return 'https://aegis-capstone.onrender.com';
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
