<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number', 'client_id', 'plan_id', 'status', 'start_date',
        'end_date', 'due_day', 'signed_price', 'discount_percent',
        'discount_type', 'discount_value', 'installation_address',
        'installation_zipcode', 'installation_neighborhood', 'installation_city',
        'installation_state', 'installation_complement', 'installation_latitude',
        'installation_longitude', 'notes', 'contract_file', 'created_by',
        'minimum_duration_months', 'cancellation_fine_formula',
        'signature_status', 'signature_id', 'signed_pdf_path', 'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'signed_at' => 'datetime',
            'signed_price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->whereDate('end_date', '>=', now());
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_type === 'percent') {
            return $this->signed_price * (1 - $this->discount_percent / 100);
        }
        return $this->signed_price - $this->discount_value;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Ativo',
            'suspended' => 'Suspenso',
            'canceled' => 'Cancelado',
            'expired' => 'Expirado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'suspended' => 'warning',
            'canceled' => 'danger',
            'expired' => 'gray',
            default => 'gray',
        };
    }
}
