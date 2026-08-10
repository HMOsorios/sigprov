<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnblockContractLinks implements ShouldQueue
{
    public function handle(PaymentConfirmed $event): void
    {
        $invoice = $event->invoice;
        $contract = $invoice->contract;

        if (!$contract || $contract->status !== 'suspended') {
            return;
        }

        $hasPendingOverdue = Invoice::where('contract_id', $contract->id)
            ->whereIn('status', ['overdue', 'pending'])
            ->exists();

        if ($hasPendingOverdue) {
            return;
        }

        $contract->update(['status' => 'active']);

        foreach ($contract->links as $link) {
            if ($link->status === 'blocked') {
                $link->update(['status' => 'active', 'blocked_at' => null]);
                $link->logs()->create([
                    'action' => 'auto_unblocked',
                    'description' => "Desbloqueio via evento PaymentConfirmed (fatura {$invoice->invoice_number})",
                ]);
            }
        }
    }
}
