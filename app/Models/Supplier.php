<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cnpj', 'legal_name', 'trade_name', 'contact', 'email',
        'phone', 'cellphone', 'zip_code', 'address', 'number',
        'complement', 'neighborhood', 'city', 'state',
        'category', 'status', 'notes',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function warehouseItems(): HasMany
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Ativo' : 'Inativo';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'equipamentos' => 'Equipamentos',
            'materiais' => 'Materiais',
            'servicos' => 'Serviços',
            default => ucfirst($this->category ?? 'Geral'),
        };
    }
}
