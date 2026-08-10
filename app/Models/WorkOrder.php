<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'contract_id', 'technician_id',
        'type', 'priority', 'status',
        'scheduled_at', 'started_at', 'finished_at',
        'description', 'resolution', 'client_signature',
        'photos', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'photos' => 'array',
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

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function techInventories()
    {
        return $this->hasMany(TechInventory::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTechnician($query, int $techId)
    {
        return $query->where('technician_id', $techId);
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled']);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'install' => 'Instalação',
            'maintenance' => 'Manutenção',
            'repair' => 'Reparo',
            'remove' => 'Retirada',
            'visit' => 'Visita Técnica',
            default => ucfirst($this->type),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'critical' => 'Crítica',
            default => ucfirst($this->priority),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'scheduled' => 'Agendada',
            'in_progress' => 'Em Andamento',
            'completed' => 'Concluída',
            'canceled' => 'Cancelada',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'scheduled' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'canceled' => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }
}
