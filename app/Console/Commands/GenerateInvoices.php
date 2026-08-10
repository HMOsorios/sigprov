<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Invoice;
use App\Services\NFService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    protected $signature = 'app:generate-invoices {--date= : Data de referência (Y-m-d)}';
    protected $description = 'Gera faturas do mês para contratos ativos com base no dia de vencimento';

    public function handle(NFService $nfService)
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $dueDay = (int) $date->format('d');
        $emitNf = config('nfse.emitir_automaticamente', false);

        $contracts = Contract::active()
            ->where('due_day', $dueDay)
            ->with(['client', 'plan'])
            ->get();

        if ($contracts->isEmpty()) {
            $this->info("Nenhum contrato com vencimento no dia {$dueDay}.");
            return 0;
        }

        $bar = $this->output->createProgressBar($contracts->count());
        $bar->start();

        $generated = 0;
        $skipped = 0;

        foreach ($contracts as $contract) {
            $existing = Invoice::where('contract_id', $contract->id)
                ->whereMonth('issue_date', $date->month)
                ->whereYear('issue_date', $date->year)
                ->exists();

            if ($existing) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $items = [
                [
                    'description' => "Plano {$contract->plan->name}",
                    'quantity' => 1,
                    'unit_price' => $contract->effective_price,
                    'total' => $contract->effective_price,
                ],
            ];

            $invoice = Invoice::create([
                'client_id' => $contract->client_id,
                'contract_id' => $contract->id,
                'invoice_number' => Invoice::max('invoice_number') + 1,
                'status' => 'pending',
                'issue_date' => $date->copy()->startOfMonth(),
                'due_date' => $date->copy()->day($contract->due_day),
                'amount' => $contract->effective_price,
                'total' => $contract->effective_price,
                'items' => $items,
                'created_by' => 1,
            ]);

            if ($emitNf) {
                try {
                    $nfService->emitir($invoice);
                } catch (\Exception $e) {
                    $this->warn("NF não emitida para fatura #{$invoice->invoice_number}: {$e->getMessage()}");
                }
            }

            $generated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Faturas geradas: {$generated}, ignoradas (já existem): {$skipped}.");
        return 0;
    }
}
