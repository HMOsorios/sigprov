<?php

namespace App\Services\Gateways;

use App\Models\Invoice;
use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoGateway implements PaymentGatewayInterface
{
    protected string $accessToken;
    protected string $baseUrl;

    public function __construct()
    {
        $this->accessToken = config('gateways.mercadopago.access_token', '');
        $this->baseUrl = config('gateways.mercadopago.environment') === 'sandbox'
            ? 'https://api.mercadopago.com/sandbox/v1'
            : 'https://api.mercadopago.com/v1';
    }

    public function createPix(Invoice $invoice): array
    {
        $payload = [
            'transaction_amount' => (float) $invoice->total,
            'description' => "Fatura {$invoice->invoice_number}",
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $invoice->client?->email,
                'first_name' => $invoice->client?->name_display,
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/\D/', '', $invoice->client?->cpf_cnpj ?? ''),
                ],
            ],
            'external_reference' => (string) $invoice->id,
        ];

        $response = $this->post('/payments', $payload);

        if (($response['status'] ?? '') === 'error') {
            Log::error('MP PIX error', $response);
            return $this->fallback($invoice);
        }

        return [
            'gateway' => 'mercadopago',
            'gateway_id' => $response['id'] ?? null,
            'pix_code' => $response['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'pix_qrcode' => $response['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
            'status' => $response['status'] ?? 'pending',
        ];
    }

    public function createBoleto(Invoice $invoice): array
    {
        $payload = [
            'transaction_amount' => (float) $invoice->total,
            'description' => "Fatura {$invoice->invoice_number}",
            'payment_method_id' => 'bolbradesco',
            'payer' => [
                'email' => $invoice->client?->email,
                'first_name' => $invoice->client?->name_display,
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/\D/', '', $invoice->client?->cpf_cnpj ?? ''),
                ],
            ],
            'external_reference' => (string) $invoice->id,
            'date_of_expiration' => $invoice->due_date->format('Y-m-d\TH:i:s.000P'),
        ];

        $response = $this->post('/payments', $payload);

        if (($response['status'] ?? '') === 'error') {
            Log::error('MP Boleto error', $response);
            return $this->fallback($invoice);
        }

        return [
            'gateway' => 'mercadopago',
            'gateway_id' => $response['id'] ?? null,
            'boleto_barcode' => $response['barcode']['content'] ?? null,
            'boleto_url' => $response['transaction_details']['external_resource_url'] ?? '#',
            'status' => $response['status'] ?? 'pending',
        ];
    }

    public function createCard(Invoice $invoice, array $cardData): array
    {
        $payload = array_merge([
            'transaction_amount' => (float) $invoice->total,
            'description' => "Fatura {$invoice->invoice_number}",
            'payer' => [
                'email' => $invoice->client?->email,
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/\D/', '', $invoice->client?->cpf_cnpj ?? ''),
                ],
            ],
            'external_reference' => (string) $invoice->id,
        ], $cardData);

        $response = $this->post('/payments', $payload);

        if (($response['status'] ?? '') === 'error') {
            Log::error('MP Card error', $response);
            return $this->fallback($invoice);
        }

        return [
            'gateway' => 'mercadopago',
            'gateway_id' => $response['id'] ?? null,
            'status' => $response['status'] ?? 'pending',
        ];
    }

    public function getStatus(string $gatewayId): string
    {
        $response = $this->get("/payments/{$gatewayId}");
        return $response['status'] ?? 'UNKNOWN';
    }

    public function cancel(string $gatewayId): bool
    {
        $payload = ['status' => 'cancelled'];
        $response = $this->put("/payments/{$gatewayId}", $payload);
        return ($response['status'] ?? '') === 'cancelled';
    }

    public function processWebhook(array $payload): array
    {
        $action = $payload['action'] ?? '';
        $data = $payload['data'] ?? [];

        if (!str_starts_with($action, 'payment.')) {
            return ['gateway' => 'mercadopago', 'ignored' => true];
        }

        $paymentId = $data['id'] ?? '';
        $payment = $this->get("/payments/{$paymentId}");

        $statusMap = [
            'approved' => 'paid',
            'in_process' => 'processing',
            'pending' => 'pending',
            'rejected' => 'failed',
            'cancelled' => 'canceled',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
        ];

        return [
            'gateway' => 'mercadopago',
            'gateway_id' => $payment['id'] ?? $paymentId,
            'invoice_id' => $payment['external_reference'] ?? null,
            'status' => $statusMap[$payment['status'] ?? ''] ?? 'pending',
            'method' => $this->mapPaymentMethod($payment['payment_method_id'] ?? ''),
            'amount' => $payment['transaction_amount'] ?? 0,
            'paid_at' => $payment['date_approved'] ?? null,
        ];
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'X-Idempotency-Key' => (string) \Illuminate\Support\Str::uuid(),
            ])->post($this->baseUrl . $endpoint, $data);

            return $response->json() ?: ['status' => 'error'];
        } catch (\Exception $e) {
            Log::error("MP POST {$endpoint}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function get(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::withHeader('Authorization', "Bearer {$this->accessToken}")
                ->get($this->baseUrl . $endpoint, $query);

            return $response->json() ?: ['status' => 'error'];
        } catch (\Exception $e) {
            Log::error("MP GET {$endpoint}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function put(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ])->put($this->baseUrl . $endpoint, $data);

            return $response->json() ?: ['status' => 'error'];
        } catch (\Exception $e) {
            Log::error("MP PUT {$endpoint}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function mapPaymentMethod(string $method): string
    {
        return match ($method) {
            'pix' => 'pix',
            'bolbradesco', 'pec' => 'boleto',
            'visa', 'master', 'amex', 'hipercard', 'elo' => 'credit_card',
            default => 'other',
        };
    }

    protected function fallback(Invoice $invoice): array
    {
        $boleto = app(\App\Services\BoletoService::class);
        return [
            'gateway' => 'local',
            'gateway_id' => null,
            ...$boleto->generateBoleto($invoice),
            ...$boleto->generatePix($invoice),
        ];
    }
}
