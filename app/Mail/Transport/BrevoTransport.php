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
            $sender = [
                'email' => $fromAddress->getAddress(),
                'name' => $fromAddress->getName() ?: null
            ];
        } else {
            $sender = [
                'email' => config('mail.from.address'),
                'name' => config('mail.from.name')
            ];
        }

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

        $response = Http::withHeaders([
            'api-key'      => $this->key,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new \Exception('Brevo API Error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo_api';
    }
}
