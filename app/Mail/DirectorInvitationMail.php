<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectorInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderEmail;
    public string $recipientEmail;
    public string $recipientName;
    public string $token;
    public string $acceptUrl;

    /**
     * Resolve the public-facing app domain for email links.
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

    public function __construct(string $senderEmail, string $recipientEmail, string $recipientName, string $token)
    {
        $this->senderEmail    = $senderEmail;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName  = $recipientName ?: 'OSA Director';
        $this->token          = $token;

        $relativeUrl    = route('director.accept-invitation', ['token' => $token], false);
        $this->acceptUrl = $this->getAppDomain() . $relativeUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A.E.G.I.S. Portal — You\'re Invited to Become the System Director',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.director_invitation',
            with: [
                'senderEmail'    => $this->senderEmail,
                'recipientEmail' => $this->recipientEmail,
                'recipientName'  => $this->recipientName,
                'acceptUrl'      => $this->acceptUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
