<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Collection;

class FinancialReportExport extends BaseExport
{
    public function export(int $year)
    {
        $monthly = Invoice::selectRaw('
                MONTH(issue_date) as month,
                YEAR(issue_date) as year,
                SUM(CASE WHEN status = "paid" THEN total ELSE 0 END) as paid,
                SUM(CASE WHEN status IN ("pending", "overdue") THEN total ELSE 0 END) as pending,
                COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_count,
                COUNT(CASE WHEN status IN ("pending", "overdue") THEN 1 END) as pending_count
            ')
            ->whereYear('issue_date', $year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $rows = $monthly->map(fn($m) => [
            'year' => $m->year,
            'month' => str_pad($m->month, 2, '0', STR_PAD_LEFT),
            'paid' => number_format($m->paid, 2, ',', '.'),
            'pending' => number_format($m->pending, 2, ',', '.'),
            'paid_count' => $m->paid_count,
            'pending_count' => $m->pending_count,
        ]);

        $headers = [
            'year' => 'Ano',
            'month' => 'Mês',
            'paid' => 'Recebido',
            'pending' => 'Pendente',
            'paid_count' => 'Qtd Recebidas',
            'pending_count' => 'Qtd Pendentes',
        ];

        return $this->toCsv($rows, $headers, "relatorio_financeiro_{$year}.csv");
    }
}
