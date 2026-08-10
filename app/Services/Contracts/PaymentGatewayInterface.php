<?php

namespace App\Services\Contracts;

use App\Models\Invoice;

interface PaymentGatewayInterface
{
    public function createPix(Invoice $invoice): array;
    public function createBoleto(Invoice $invoice): array;
    public function createCard(Invoice $invoice, array $cardData): array;
    public function getStatus(string $gatewayId): string;
    public function cancel(string $gatewayId): bool;
    public function processWebhook(array $payload): array;
}
