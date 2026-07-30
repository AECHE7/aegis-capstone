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
