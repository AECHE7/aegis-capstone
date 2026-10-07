<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasterTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $senderEmail;
    public $recipientEmail;
    public $token;
    public $acceptUrl;

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
     * Create a new message instance.
     */
    public function __construct(string $senderEmail, string $recipientEmail, string $token)
    {
        $this->senderEmail = $senderEmail;
        $this->recipientEmail = $recipientEmail;
        $this->token = $token;
        
        $relativeUrl = route('master.accept-transfer', ['token' => $token], false);
        $this->acceptUrl = $this->getAppDomain() . $relativeUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A.E.G.I.S. Portal — Master Privilege Transfer Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.master_transfer',
            with: [
                'senderEmail' => $this->senderEmail,
                'recipientEmail' => $this->recipientEmail,
                'acceptUrl' => $this->acceptUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
