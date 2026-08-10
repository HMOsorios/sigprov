<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'app:check-overdue-invoices';
    protected $description = 'Marca faturas pendentes como vencidas quando a data de vencimento já passou';

    public function handle()
    {
        $updated = Invoice::where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);

        $this->info("Faturas marcadas como vencidas: {$updated}.");
        return 0;
    }
}
