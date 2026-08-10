<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoBlockOverdue extends Command
{
    protected $signature = 'app:auto-block-overdue {--days=10 : Dias após vencimento para bloquear}';
    protected $description = 'Suspende contratos e bloqueia links de clientes com faturas vencidas além do prazo';

    public function handle()
    {
        $graceDays = (int) $this->option('days');
        $cutoff = Carbon::today()->subDays($graceDays);

        $overdueInvoices = Invoice::where('status', 'overdue')
            ->where('due_date', '<', $cutoff)
            ->with('contract.links')
            ->get();

        $blocked = 0;

        foreach ($overdueInvoices as $invoice) {
            $contract = $invoice->contract;
            if (!$contract || $contract->status !== 'active') {
                continue;
            }

            $contract->update(['status' => 'suspended']);

            foreach ($contract->links as $link) {
                if ($link->status === 'active') {
                    $link->update([
                        'status' => 'blocked',
                        'blocked_at' => Carbon::now(),
                    ]);
                    $link->logs()->create([
                        'action' => 'auto_blocked',
                        'description' => "Bloqueio automático por inadimplência (fatura {$invoice->invoice_number})",
                        'metadata' => ['invoice_id' => $invoice->id],
                    ]);
                }
            }

            $blocked++;
        }

        $this->info("Contratos suspensos/bloqueados: {$blocked}.");
        return 0;
    }
}
