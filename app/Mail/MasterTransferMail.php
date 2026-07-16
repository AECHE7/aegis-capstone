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
     * Create a new message instance.
     */
    public function __construct(string $senderEmail, string $recipientEmail, string $token)
    {
        $this->senderEmail = $senderEmail;
        $this->recipientEmail = $recipientEmail;
        $this->token = $token;
        // Generate acceptance URL
        $this->acceptUrl = route('master.accept-transfer', ['token' => $token]);
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
