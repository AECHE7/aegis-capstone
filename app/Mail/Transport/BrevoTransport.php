<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;

class BrevoTransport extends AbstractTransport
{
    protected string $key;

    public function __construct(string $key)
    {
        parent::__construct();
        $this->key = $key;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: null
            ];
        }

        $sender = null;
        if ($email->getFrom()) {
            $fromAddress = $email->getFrom()[0];
            $senderEmail = $fromAddress->getAddress();
            $senderName = $fromAddress->getName() ?: null;
        } else {
            $senderEmail = config('mail.from.address', 'gadianoriel07@gmail.com');
            $senderName = config('mail.from.name', 'AEGIS CLSU');
        }

        // Brevo strictly validates verified senders — prevent invalid or placeholder addresses
        if (empty($senderEmail) || str_contains($senderEmail, 'example.com')) {
            $senderEmail = config('mail.from.address', 'gadianoriel07@gmail.com');
            $senderName = config('mail.from.name', 'AEGIS CLSU');
        }

        $sender = [
            'email' => $senderEmail,
            'name' => $senderName
        ];

        $payload = [
            'sender'      => $sender,
            'to'          => $to,
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: $email->getTextBody(),
        ];

        // Include any attachments (e.g. approval PDF)
        $attachments = [];
        foreach ($email->getAttachments() as $part) {
            $attachments[] = [
                'content' => base64_encode($part->getBody()),
                'name'    => $part->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')
                              ?: ($part->getPreparedHeaders()->getHeaderParameter('Content-Type', 'name') ?: 'attachment'),
            ];
        }
        if (!empty($attachments)) {
            $payload['attachment'] = $attachments;
        }

        $apiKey = !empty($this->key) ? $this->key : config('mail.mailers.brevo_api.key', '');
        if (empty($apiKey)) {
            \Illuminate\Support\Facades\Log::error('Brevo API key is not configured. Set BREVO_API_KEY environment variable.');
            throw new \Exception('Brevo API Error: API key not configured.');
        }

        $response = Http::timeout(15)->withHeaders([
            'api-key'      => $apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error('Brevo API Error (' . $response->status() . '): ' . $response->body());
            throw new \Exception('Brevo API Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo_api';
    }
}
