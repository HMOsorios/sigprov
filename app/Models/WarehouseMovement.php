<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseMovement extends Model
{
    protected $fillable = [
        'item_id', 'type', 'qty', 'unit_price',
        'reference_type', 'reference_id', 'responsible_id',
        'notes', 'movement_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'movement_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WarehouseItem::class, 'item_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function scopeInbound($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOutbound($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeByReference($query, string $type, int $id)
    {
        return $query->where('reference_type', $type)
            ->where('reference_id', $id);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Entrada' : 'Saída';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }

    public function getReferenceTypeLabelAttribute(): string
    {
        return match ($this->reference_type) {
            'purchase' => 'Compra',
            'sale' => 'Venda',
            'transfer' => 'Transferência',
            'adjustment' => 'Ajuste',
            'return' => 'Devolução',
            'install' => 'Instalação',
            'maintenance' => 'Manutenção',
            default => ucfirst($this->reference_type ?? ''),
        };
    }
}
