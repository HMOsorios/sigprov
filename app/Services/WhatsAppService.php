<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.base_url', 'http://localhost:8080');
        $this->apiKey = config('services.whatsapp.api_key', '');
        $this->instance = config('services.whatsapp.instance', 'sisprov');
    }

    public function sendText(string $to, string $message): array
    {
        return $this->post('/message/sendText', [
            'number' => $this->normalizeNumber($to),
            'text' => $message,
        ]);
    }

    public function sendTemplate(string $to, string $templateName, array $params = []): array
    {
        return $this->post('/message/sendTemplate', [
            'number' => $this->normalizeNumber($to),
            'template' => $templateName,
            'params' => $params,
        ]);
    }

    public function sendDocument(string $to, string $url, string $filename): array
    {
        return $this->post('/message/sendMedia', [
            'number' => $this->normalizeNumber($to),
            'media' => $url,
            'fileName' => $filename,
        ]);
    }

    public function sendImage(string $to, string $url, string $caption = ''): array
    {
        return $this->post('/message/sendMedia', [
            'number' => $this->normalizeNumber($to),
            'media' => $url,
            'caption' => $caption,
        ]);
    }

    public function processWebhook(array $payload): ?array
    {
        $messageType = $payload['type'] ?? null;
        $from = $payload['from'] ?? null;
        $body = $payload['text'] ?? $payload['body'] ?? null;

        if (!$from || !$body) {
            return null;
        }

        return [
            'from' => $this->cleanNumber($from),
            'type' => $messageType,
            'body' => $body,
            'raw' => $payload,
        ];
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'apiKey' => $this->apiKey,
                ])
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return ['status' => 'success', 'data' => $response->json()];
            }

            Log::warning('WhatsApp API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['status' => 'error', 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('WhatsApp API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function normalizeNumber(string $number): string
    {
        $clean = preg_replace('/\D/', '', $number);
        if (strlen($clean) === 11) {
            $clean = '55' . $clean;
        }
        if (strlen($clean) === 12 && !str_starts_with($clean, '55')) {
            $clean = '55' . $clean;
        }
        return $clean . '@c.us';
    }

    protected function cleanNumber(string $number): string
    {
        $clean = preg_replace('/\D/', '', $number);
        if (strlen($clean) > 11) {
            $clean = substr($clean, -11);
        }
        return $clean;
    }
}
