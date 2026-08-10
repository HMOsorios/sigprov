<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Collection;

class InvoiceExport extends BaseExport
{
    public function export(array $filters = [])
    {
        $query = Invoice::with(['client', 'contract.plan']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['start_date'])) {
            $query->where('issue_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('issue_date', '<=', $filters['end_date']);
        }

        $invoices = $query->latest()->get();

        $rows = $invoices->map(fn($i) => [
            'id' => $i->id,
            'invoice_number' => $i->invoice_number,
            'client' => $i->client?->name_display ?? '-',
            'contract' => $i->contract?->contract_number ?? '-',
            'plan' => $i->contract?->plan?->name ?? '-',
            'total' => $i->total,
            'status' => $i->status_label,
            'issue_date' => $i->issue_date?->format('d/m/Y'),
            'due_date' => $i->due_date?->format('d/m/Y'),
            'paid_at' => $i->paid_at?->format('d/m/Y'),
            'payment_method' => $i->payment_method ?? '-',
        ]);

        $headers = [
            'id' => 'ID',
            'invoice_number' => 'Fatura',
            'client' => 'Cliente',
            'contract' => 'Contrato',
            'plan' => 'Plano',
            'total' => 'Valor',
            'status' => 'Status',
            'issue_date' => 'Emissão',
            'due_date' => 'Vencimento',
            'paid_at' => 'Pagamento',
            'payment_method' => 'Forma',
        ];

        return $this->toCsv($rows, $headers, 'faturas.csv');
    }
}
