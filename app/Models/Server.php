<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'hostname', 'ip_address', 'port', 'type', 'brand', 'model',
        'firmware_version', 'location', 'username', 'encrypted_password',
        'ssh_key', 'status', 'last_ping_at', 'cpu_usage', 'memory_usage',
        'disk_usage', 'notes', 'monitoring_config', 'is_monitored',
        'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'last_ping_at' => 'datetime',
            'cpu_usage' => 'decimal:2',
            'memory_usage' => 'decimal:2',
            'disk_usage' => 'decimal:2',
            'monitoring_config' => 'array',
            'is_monitored' => 'boolean',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function logs()
    {
        return $this->hasMany(ServerLog::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'online' => 'Online',
            'offline' => 'Offline',
            'maintenance' => 'Em Manutenção',
            'error' => 'Erro',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'online' => 'success',
            'offline' => 'danger',
            'maintenance' => 'warning',
            'error' => 'danger',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'router' => 'Roteador',
            'switch' => 'Switch',
            'server' => 'Servidor',
            'firewall' => 'Firewall',
            'nas' => 'NAS',
            'radius' => 'Radius',
            'dhcp' => 'DHCP',
            'dns' => 'DNS',
            'other' => 'Outro',
            default => $this->type,
        };
    }
}
