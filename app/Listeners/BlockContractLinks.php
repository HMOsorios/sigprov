<?php

namespace App\Listeners;

use App\Events\ContractSuspended;
use Illuminate\Contracts\Queue\ShouldQueue;

class BlockContractLinks implements ShouldQueue
{
    public function handle(ContractSuspended $event): void
    {
        $contract = $event->contract;

        foreach ($contract->links as $link) {
            if ($link->status === 'active') {
                $link->update([
                    'status' => 'blocked',
                    'blocked_at' => now(),
                ]);
                $link->logs()->create([
                    'action' => 'auto_blocked',
                    'description' => "Bloqueio via evento ContractSuspended (contrato {$contract->contract_number})",
                ]);
            }
        }
    }
}
