<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpPool extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'subnet', 'gateway', 'dns1', 'dns2',
        'range_start', 'range_end', 'type', 'is_cgnat',
        'server_id', 'used', 'total', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_cgnat' => 'boolean',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(IpAssignment::class, 'pool_id');
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(IpAssignment::class, 'pool_id')->whereNull('released_at');
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeIpv4($query)
    {
        return $query->where('type', 'ipv4');
    }

    public function isFull(): bool
    {
        return $this->used >= $this->total;
    }

    public function getUsagePercentAttribute(): float
    {
        if ($this->total === 0) {
            return 0;
        }
        return round(($this->used / $this->total) * 100, 1);
    }

    public function getRangeAttribute(): string
    {
        return "{$this->range_start} - {$this->range_end}";
    }
}
