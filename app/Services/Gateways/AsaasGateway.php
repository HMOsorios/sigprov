<?php

namespace App\Services\Gateways;

use App\Models\Invoice;
use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasGateway implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('gateways.asaas.api_key', '');
        $this->baseUrl = config('gateways.asaas.environment') === 'sandbox'
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://api.asaas.com/v3';
    }

    public function createPix(Invoice $invoice): array
    {
        $payload = [
            'customer' => $this->getCustomerId($invoice),
            'billingType' => 'PIX',
            'value' => $invoice->total,
            'dueDate' => $invoice->due_date->format('Y-m-d'),
            'description' => "Fatura {$invoice->invoice_number}",
            'externalReference' => (string) $invoice->id,
        ];

        $response = $this->post('/payments', $payload);

        if ($response['status'] === 'error') {
            Log::error('Asaas PIX error', $response);
            return $this->fallback($invoice);
        }

        $pixResponse = $this->get("/payments/{$response['id']}/pixQrCode");

        return [
            'gateway' => 'asaas',
            'gateway_id' => $response['id'],
            'pix_code' => $pixResponse['payload'] ?? null,
            'pix_qrcode' => $pixResponse['encodedImage'] ?? null,
            'status' => $response['status'],
        ];
    }

    public function createBoleto(Invoice $invoice): array
    {
        $payload = [
            'customer' => $this->getCustomerId($invoice),
            'billingType' => 'BOLETO',
            'value' => $invoice->total,
            'dueDate' => $invoice->due_date->format('Y-m-d'),
            'description' => "Fatura {$invoice->invoice_number}",
            'externalReference' => (string) $invoice->id,
        ];

        $response = $this->post('/payments', $payload);

        if ($response['status'] === 'error') {
            Log::error('Asaas Boleto error', $response);
            return $this->fallback($invoice);
        }

        return [
            'gateway' => 'asaas',
            'gateway_id' => $response['id'],
            'boleto_barcode' => $response['barcode'] ?? null,
            'boleto_url' => $response['bankSlipUrl'] ?? '#',
            'status' => $response['status'],
        ];
    }

    public function createCard(Invoice $invoice, array $cardData): array
    {
        $payload = array_merge([
            'customer' => $this->getCustomerId($invoice),
            'billingType' => 'CREDIT_CARD',
            'value' => $invoice->total,
            'dueDate' => $invoice->due_date->format('Y-m-d'),
            'description' => "Fatura {$invoice->invoice_number}",
            'externalReference' => (string) $invoice->id,
        ], $cardData);

        $response = $this->post('/payments', $payload);

        if ($response['status'] === 'error') {
            Log::error('Asaas Card error', $response);
            return $this->fallback($invoice);
        }

        return [
            'gateway' => 'asaas',
            'gateway_id' => $response['id'],
            'status' => $response['status'],
        ];
    }

    public function getStatus(string $gatewayId): string
    {
        $response = $this->get("/payments/{$gatewayId}");
        return $response['status'] ?? 'UNKNOWN';
    }

    public function cancel(string $gatewayId): bool
    {
        $response = $this->post("/payments/{$gatewayId}/cancel", []);
        return ($response['status'] ?? '') === 'CANCELED';
    }

    public function processWebhook(array $payload): array
    {
        $event = $payload['event'] ?? '';
        $payment = $payload['payment'] ?? [];

        $statusMap = [
            'PAYMENT_RECEIVED' => 'paid',
            'PAYMENT_CONFIRMED' => 'paid',
            'PAYMENT_OVERDUE' => 'overdue',
            'PAYMENT_CANCELED' => 'canceled',
            'PAYMENT_REFUNDED' => 'refunded',
            'PAYMENT_FAILED' => 'failed',
        ];

        return [
            'gateway' => 'asaas',
            'gateway_id' => $payment['id'] ?? '',
            'invoice_id' => $payment['externalReference'] ?? null,
            'status' => $statusMap[$event] ?? 'pending',
            'method' => $this->mapBillingType($payment['billingType'] ?? ''),
            'amount' => $payment['value'] ?? 0,
            'paid_at' => $payment['paymentDate'] ?? null,
        ];
    }

    protected function getCustomerId(Invoice $invoice): ?string
    {
        $client = $invoice->client;
        if (!$client) {
            return null;
        }

        $customers = $this->get('/customers', ['cpfCnpj' => $client->cpf_cnpj]);
        if (!empty($customers['data'])) {
            return $customers['data'][0]['id'];
        }

        $response = $this->post('/customers', [
            'name' => $client->name_display,
            'email' => $client->email,
            'phone' => $client->cellphone ?: $client->phone,
            'cpfCnpj' => $client->cpf_cnpj,
        ]);

        return $response['id'] ?? null;
    }

    protected function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeader('access_token', $this->apiKey)
                ->post($this->baseUrl . $endpoint, $data);

            return $response->json() ?: ['status' => 'error'];
        } catch (\Exception $e) {
            Log::error("Asaas POST {$endpoint}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function get(string $endpoint, array $query = []): array
    {
        try {
            $response = Http::withHeader('access_token', $this->apiKey)
                ->get($this->baseUrl . $endpoint, $query);

            return $response->json() ?: ['status' => 'error'];
        } catch (\Exception $e) {
            Log::error("Asaas GET {$endpoint}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function mapBillingType(string $type): string
    {
        return match ($type) {
            'BOLETO' => 'boleto',
            'PIX' => 'pix',
            'CREDIT_CARD' => 'credit_card',
            'DEBIT_CARD' => 'debit_card',
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
