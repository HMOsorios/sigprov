<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentAssignment extends Model
{
    protected $fillable = [
        'equipment_id', 'client_id', 'contract_id', 'assigned_by',
        'assigned_at', 'returned_at', 'condition_out', 'condition_in', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    public function isActive(): bool
    {
        return $this->returned_at === null;
    }

    public function getConditionOutLabelAttribute(): string
    {
        return match ($this->condition_out) {
            'novo' => 'Novo',
            'bom' => 'Bom',
            'regular' => 'Regular',
            'danificado' => 'Danificado',
            default => $this->condition_out ?? '-',
        };
    }

    public function getConditionInLabelAttribute(): string
    {
        return match ($this->condition_in) {
            'novo' => 'Novo',
            'bom' => 'Bom',
            'regular' => 'Regular',
            'danificado' => 'Danificado',
            default => $this->condition_in ?? '-',
        };
    }
}
