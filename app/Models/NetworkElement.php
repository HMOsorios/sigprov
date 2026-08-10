<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetworkElement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'model', 'serial', 'parent_id',
        'order', 'identifier', 'latitude', 'longitude',
        'address', 'city', 'state', 'server_id',
        'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRootElements($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'olt' => 'OLT',
            'splitter' => 'Splitter',
            'cto' => 'CTO',
            'drop' => 'Drop',
            'client' => 'Cliente',
            'caixa' => 'Caixa de Passagem',
            'armario' => 'Armário',
            'backbone' => 'Backbone',
            default => ucfirst($this->type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Ativo' : 'Inativo';
    }

    public function childrenRecursive(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('childrenRecursive');
    }
}
