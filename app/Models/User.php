<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'cpf_cnpj', 'phone', 'avatar',
        'is_active', 'two_factor_enabled', 'two_factor_secret',
        'two_factor_recovery_codes', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role?->permissions()->where('name', $permission)->exists() ?? false;
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role?->name, $roles);
    }

    public function isDeveloper(): bool
    {
        return $this->role?->name === 'developer';
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isTechnician(): bool
    {
        return $this->role?->name === 'technician';
    }

    public function isAdministrativo(): bool
    {
        return $this->role?->name === 'administrativo';
    }

    public function isClient(): bool
    {
        return $this->role?->name === 'client';
    }

    public function isStaff(): bool
    {
        return in_array($this->role?->name, ['developer', 'admin', 'technician', 'administrativo']);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_user')
            ->withPivot('is_main_contact');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
