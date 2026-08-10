<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'label', 'description', 'is_locked'];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function givePermissionTo(string|array $permissions): void
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $ids = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function revokePermission(string|array $permissions): void
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $ids = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->detach($ids);
    }

    public function syncPermissions(array $permissions): void
    {
        $ids = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->sync($ids);
    }
}
