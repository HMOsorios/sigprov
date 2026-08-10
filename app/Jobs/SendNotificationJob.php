<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Invoice $invoice,
        public string $channel,
        public string $template,
        public array $data = [],
    ) {}

    public function handle(): void
    {
        $client = $this->invoice->client;
        if (!$client) {
            return;
        }

        $log = "[{$this->channel}] Fatura {$this->invoice->invoice_number} -> {$client->name_display}: {$this->template}";
        activity('notification')->log($log);
    }
}
