<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NfTelecom extends Model
{
    use SoftDeletes;

    protected $table = 'nf_telecom';

    protected $fillable = [
        'invoice_id', 'client_id', 'contract_id',
        'numero', 'serie', 'competencia', 'modelo', 'valor',
        'xml', 'protocolo', 'chave_acesso', 'link_danfe',
        'status', 'motivos_rejeicao', 'emitida_em', 'cancelada_em',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'motivos_rejeicao' => 'array',
            'emitida_em' => 'datetime',
            'cancelada_em' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function scopeByCompetencia($query, string $competencia)
    {
        return $query->where('competencia', $competencia);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente' => 'Pendente',
            'autorizada' => 'Autorizada',
            'cancelada' => 'Cancelada',
            'rejeitada' => 'Rejeitada',
            'denegada' => 'Denegada',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'autorizada' => 'success',
            'pendente' => 'warning',
            'cancelada' => 'gray',
            'rejeitada' => 'danger',
            'denegada' => 'danger',
            default => 'gray',
        };
    }

    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}
