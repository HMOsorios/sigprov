<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'download_speed', 'upload_speed',
        'speed_unit', 'monthly_traffic', 'traffic_type', 'price', 'setup_fee',
        'contract_duration', 'billing_cycle', 'max_connections', 'technology',
        'has_static_ip', 'static_ip_qty', 'is_active', 'is_featured',
        'order', 'features', 'fine_print', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'download_speed' => 'decimal:2',
            'upload_speed' => 'decimal:2',
            'price' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'has_static_ip' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'array',
            'fine_print' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name) . '-' . uniqid();
            }
        });
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getSpeedLabelAttribute(): string
    {
        return "{$this->download_speed} {$this->speed_unit}";
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return match ($this->billing_cycle) {
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual' => 'Anual',
            default => $this->billing_cycle,
        };
    }

    public function getTrafficTypeLabelAttribute(): string
    {
        return match ($this->traffic_type) {
            'unlimited' => 'Ilimitado',
            'limited' => 'Limitado',
            'fup' => 'FUP',
            default => $this->traffic_type,
        };
    }
}
