<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku', 'name', 'category', 'unit', 'unit_price', 'cost_price',
        'min_stock', 'max_stock', 'current_qty', 'location',
        'supplier_id', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(WarehouseMovement::class, 'item_id');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_qty', '<=', 'min_stock')
            ->where('current_qty', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_qty', '<=', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isLowStock(): bool
    {
        return $this->current_qty <= $this->min_stock && $this->current_qty > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_qty <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_qty <= 0) {
            return 'Sem Estoque';
        }
        if ($this->current_qty <= $this->min_stock) {
            return 'Estoque Baixo';
        }
        return 'Normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->current_qty <= 0) {
            return 'danger';
        }
        if ($this->current_qty <= $this->min_stock) {
            return 'warning';
        }
        return 'success';
    }

    public function getTotalValueAttribute(): float
    {
        return $this->current_qty * $this->unit_price;
    }
}
