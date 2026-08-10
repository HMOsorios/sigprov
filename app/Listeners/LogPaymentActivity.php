<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPaymentActivity implements ShouldQueue
{
    public function handle(PaymentConfirmed $event): void
    {
        $invoice = $event->invoice;

        activity('payment')->log("Pagamento confirmado para fatura {$invoice->invoice_number}");
    }
}
