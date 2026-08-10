<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number', 'client_id', 'contract_id', 'assigned_to',
        'subject', 'priority', 'category', 'status',
        'resolved_at', 'closed_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_client']);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'critical' => 'Crítica',
            default => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Aberto',
            'in_progress' => 'Em Andamento',
            'waiting_client' => 'Aguardando Cliente',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'waiting_client' => 'info',
            'resolved' => 'success',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'technical' => 'Técnico',
            'billing' => 'Financeiro',
            'commercial' => 'Comercial',
            'installation' => 'Instalação',
            'complaint' => 'Reclamação',
            'other' => 'Outro',
            default => $this->category,
        };
    }
}
