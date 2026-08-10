<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechInventory extends Model
{
    protected $fillable = [
        'technician_id', 'equipment_id', 'work_order_id',
        'checked_out_at', 'checked_in_at',
        'condition_out', 'condition_in', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'checked_out_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('checked_in_at');
    }

    public function scopeByTechnician($query, int $techId)
    {
        return $query->where('technician_id', $techId);
    }

    public function isCheckedOut(): bool
    {
        return $this->checked_in_at === null;
    }
}
