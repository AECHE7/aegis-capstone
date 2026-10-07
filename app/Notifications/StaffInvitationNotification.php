<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Contracts\Queue\ShouldQueue;

class StaffInvitationNotification extends Notification implements ShouldQueue
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
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $activationUrl = $this->getAppDomain() . '/activate-account?token=' . $this->token;

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
            ->view('emails.staff_invite', [
                'name' => $notifiable->name,
                'activationUrl' => $activationUrl
            ]);
    }
}
