<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoUnblockPaid extends Command
{
    protected $signature = 'app:auto-unblock-paid';
    protected $description = 'Reativa contratos e desbloqueia links de clientes que regularizaram faturas';

    public function handle()
    {
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereHas('contract', function ($q) {
                $q->where('status', 'suspended');
            })
            ->with('contract.links')
            ->get();

        $reactivated = 0;

        foreach ($paidInvoices as $invoice) {
            $contract = $invoice->contract;
            if (!$contract) {
                continue;
            }

            $hasPendingOverdue = Invoice::where('contract_id', $contract->id)
                ->whereIn('status', ['overdue', 'pending'])
                ->exists();

            if ($hasPendingOverdue) {
                continue;
            }

            $contract->update(['status' => 'active']);

            foreach ($contract->links as $link) {
                if ($link->status === 'blocked') {
                    $link->update([
                        'status' => 'active',
                        'blocked_at' => null,
                    ]);
                    $link->logs()->create([
                        'action' => 'auto_unblocked',
                        'description' => "Desbloqueio automático após pagamento (fatura {$invoice->invoice_number})",
                        'metadata' => ['invoice_id' => $invoice->id],
                    ]);
                }
            }

            $reactivated++;
        }

        $this->info("Contratos reativados/desbloqueados: {$reactivated}.");
        return 0;
    }
}
