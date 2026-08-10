<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public Contract $contract, public Carbon $referenceDate) {}

    public function handle(): void
    {
        $exists = Invoice::where('contract_id', $this->contract->id)
            ->whereMonth('issue_date', $this->referenceDate->month)
            ->whereYear('issue_date', $this->referenceDate->year)
            ->exists();

        if ($exists) {
            return;
        }

        $items = [
            [
                'description' => "Plano {$this->contract->plan->name}",
                'quantity' => 1,
                'unit_price' => $this->contract->effective_price,
                'total' => $this->contract->effective_price,
            ],
        ];

        Invoice::create([
            'client_id' => $this->contract->client_id,
            'contract_id' => $this->contract->id,
            'invoice_number' => Invoice::max('invoice_number') + 1,
            'status' => 'pending',
            'issue_date' => $this->referenceDate->copy()->startOfMonth(),
            'due_date' => $this->referenceDate->copy()->day($this->contract->due_day),
            'amount' => $this->contract->effective_price,
            'total' => $this->contract->effective_price,
            'items' => $items,
            'created_by' => 1,
        ]);
    }
}
