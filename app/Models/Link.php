<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id', 'server_id', 'pppoe_user', 'pppoe_password',
        'ip_address', 'mac_address', 'vlan', 'ont_serial', 'ont_brand',
        'ont_model', 'cable_origin', 'cable_drop', 'splitter_location',
        'signal_rx', 'signal_tx', 'status', 'last_sync_at',
        'activated_at', 'blocked_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_sync_at' => 'datetime',
            'activated_at' => 'datetime',
            'blocked_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function logs()
    {
        return $this->hasMany(LinkLog::class);
    }

    public function client()
    {
        return $this->hasOneThrough(Client::class, Contract::class, 'id', 'id', 'contract_id', 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Ativo',
            'inactive' => 'Inativo',
            'blocked' => 'Bloqueado',
            'maintenance' => 'Manutenção',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'gray',
            'blocked' => 'danger',
            'maintenance' => 'warning',
            default => 'gray',
        };
    }
}
