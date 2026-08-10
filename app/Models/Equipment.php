<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'serial', 'patrimony', 'brand', 'model', 'type',
        'mac', 'ip_address', 'firmware_version',
        'purchase_price', 'purchase_date', 'supplier_id',
        'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(EquipmentAssignment::class)
            ->whereNull('returned_at')
            ->latest('assigned_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'emprestado');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponivel';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disponivel' => 'Disponível',
            'emprestado' => 'Emprestado',
            'manutencao' => 'Em Manutenção',
            'descartado' => 'Descartado',
            'perdido' => 'Perdido',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'disponivel' => 'success',
            'emprestado' => 'warning',
            'manutencao' => 'info',
            'descartado' => 'danger',
            'perdido' => 'danger',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'router' => 'Roteador',
            'onu' => 'ONU',
            'modem' => 'Modem',
            'cto' => 'CTO',
            'splitter' => 'Splitter',
            'ont' => 'ONT',
            'ups' => 'UPS',
            'cabo' => 'Cabo',
            'fonte' => 'Fonte',
            'antena' => 'Antena',
            default => ucfirst($this->type),
        };
    }
}
