<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'client_id', 'contract_id', 'status',
        'issue_date', 'due_date', 'paid_date', 'amount', 'discount',
        'late_fee', 'interest', 'total', 'boleto_barcode', 'boleto_url',
        'pix_code', 'pix_qrcode', 'notes', 'payment_gateway',
        'payment_gateway_id', 'paid_amount', 'payment_method',
        'items', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'datetime',
            'paid_amount' => 'decimal:2',
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'late_fee' => 'decimal:2',
            'interest' => 'decimal:2',
            'total' => 'decimal:2',
            'items' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nfTelecom()
    {
        return $this->hasOne(NfTelecom::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                    ->whereDate('due_date', '<', now());
            });
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('issue_date', [$start, $end]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'overdue' => 'Vencida',
            'paid' => 'Paga',
            'canceled' => 'Cancelada',
            'refunded' => 'Estornada',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'overdue' => 'danger',
            'paid' => 'success',
            'canceled' => 'gray',
            'refunded' => 'info',
            default => 'gray',
        };
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'R$ ' . number_format($this->total, 2, ',', '.');
    }
}
