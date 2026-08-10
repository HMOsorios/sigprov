<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'payment_code', 'method', 'status', 'amount',
        'fee', 'net_amount', 'gateway', 'gateway_id', 'gateway_response',
        'paid_at', 'notes', 'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'boleto' => 'Boleto',
            'pix' => 'PIX',
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'transfer' => 'Transferência',
            'cash' => 'Dinheiro',
            'other' => 'Outro',
            default => $this->method,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'failed' => 'Falhou',
            'refunded' => 'Estornado',
            default => $this->status,
        };
    }
}
