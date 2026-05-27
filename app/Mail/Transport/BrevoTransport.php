<?php

namespace App\Mail\Transport;

use GuzzleHttp\Client;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mailer\Transport\AbstractTransport;

/**
 * Brevo (Sendinblue) HTTP API Transport para Laravel 10+
 * Usa la REST API de Brevo en vez de SMTP.
 */
class BrevoTransport extends AbstractTransport
{
    protected Client $http;
    protected string $apiKey;
    protected string $endpoint = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->http   = new Client();
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = [
            'sender'  => $this->buildSender($email),
            'to'      => $this->buildAddresses($email->getTo()),
            'subject' => $email->getSubject(),
        ];

        // Contenido
        if ($html = $email->getHtmlBody()) {
            $payload['htmlContent'] = $html;
        }
        if ($text = $email->getTextBody()) {
            $payload['textContent'] = $text;
        }

        // CC / BCC / Reply-To
        if ($cc = $email->getCc()) {
            $payload['cc'] = $this->buildAddresses($cc);
        }
        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = $this->buildAddresses($bcc);
        }
        if ($replyTo = $email->getReplyTo()) {
            $first = collect($replyTo)->first();
            if ($first) {
                $payload['replyTo'] = [
                    'email' => $first->getAddress(),
                    'name'  => $first->getName() ?: '',
                ];
            }
        }

        $this->http->post($this->endpoint, [
            'headers' => [
                'api-key'      => $this->apiKey,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);
    }

    private function buildSender(\Symfony\Component\Mime\Email $email): array
    {
        $from = collect($email->getFrom())->first();
        $sender = ['email' => $from->getAddress()];
        
        $name = $from->getName() ?: config('mail.from.name', 'Hotel Oasis');
        if (!empty($name)) {
            $sender['name'] = $name;
        }

        return $sender;
    }

    private function buildAddresses(array $addresses): array
    {
        return collect($addresses)->map(function ($addr) {
            $data = ['email' => $addr->getAddress()];
            $name = $addr->getName();
            if (!empty($name)) {
                $data['name'] = $name;
            }
            return $data;
        })->toArray();
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
