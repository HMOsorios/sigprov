<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'cellphone', 'interest_plan', 'source',
        'status', 'notes', 'address', 'city', 'state', 'assigned_to',
        'converted_client_id', 'converted_at', 'last_contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'converted_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedClient()
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Novo',
            'contacted' => 'Contactado',
            'proposal' => 'Proposta',
            'negotiation' => 'Negociação',
            'won' => 'Ganho',
            'lost' => 'Perdido',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-blue-100 text-blue-800',
            'contacted' => 'bg-yellow-100 text-yellow-800',
            'proposal' => 'bg-purple-100 text-purple-800',
            'negotiation' => 'bg-orange-100 text-orange-800',
            'won' => 'bg-emerald-100 text-emerald-800',
            'lost' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
