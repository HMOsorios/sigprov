<?php

namespace App\Exports;

use App\Models\Ticket;

class TicketExport extends BaseExport
{
    public function export(array $filters = [])
    {
        $query = Ticket::with(['client', 'assignedTo']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        $tickets = $query->latest()->get();

        $rows = $tickets->map(fn($t) => [
            'id' => $t->id,
            'ticket_number' => $t->ticket_number,
            'client' => $t->client?->name_display ?? '-',
            'subject' => $t->subject,
            'category' => $t->category_label,
            'priority' => $t->priority_label,
            'status' => $t->status_label,
            'assigned_to' => $t->assignedTo?->name ?? '-',
            'created_at' => $t->created_at->format('d/m/Y H:i'),
            'resolved_at' => $t->resolved_at?->format('d/m/Y H:i') ?? '-',
        ]);

        $headers = [
            'id' => 'ID',
            'ticket_number' => 'Chamado',
            'client' => 'Cliente',
            'subject' => 'Assunto',
            'category' => 'Categoria',
            'priority' => 'Prioridade',
            'status' => 'Status',
            'assigned_to' => 'Responsável',
            'created_at' => 'Abertura',
            'resolved_at' => 'Resolução',
        ];

        return $this->toCsv($rows, $headers, 'chamados.csv');
    }
}
