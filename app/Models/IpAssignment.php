<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpAssignment extends Model
{
    protected $fillable = [
        'pool_id', 'link_id', 'ip_address', 'mac_address',
        'assigned_at', 'released_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(IpPool::class, 'pool_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('released_at');
    }

    public function isActive(): bool
    {
        return $this->released_at === null;
    }

    public static function getNextAvailable(IpPool $pool): ?string
    {
        $used = self::where('pool_id', $pool->id)
            ->whereNull('released_at')
            ->pluck('ip_address')
            ->toArray();

        $start = ip2long($pool->range_start);
        $end = ip2long($pool->range_end);

        for ($ip = $start; $ip <= $end; $ip++) {
            $address = long2ip($ip);
            if (!in_array($address, $used)) {
                return $address;
            }
        }

        return null;
    }
}
