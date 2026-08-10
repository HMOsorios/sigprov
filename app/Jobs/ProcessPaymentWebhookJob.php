<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentWebhookJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $gateway,
        public string $gatewayId,
        public string $status,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $invoiceId = $this->payload['invoice_id'] ?? null;
        if (!$invoiceId) {
            return;
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            return;
        }

        if ($this->status === 'paid') {
            $invoice->update([
                'status' => 'paid',
                'paid_date' => Carbon::now(),
            ]);

            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_code' => Payment::max('payment_code') + 1,
                'method' => $this->payload['method'] ?? 'pix',
                'status' => 'confirmed',
                'amount' => $invoice->total,
                'gateway' => $this->gateway,
                'gateway_id' => $this->gatewayId,
                'gateway_response' => json_encode($this->payload),
                'paid_at' => Carbon::now(),
            ]);
        }
    }
}
