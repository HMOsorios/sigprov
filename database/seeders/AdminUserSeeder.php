<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $devRole = Role::where('name', 'developer')->first();
        if ($devRole && !User::where('email', 'developer@sisprov.com.br')->exists()) {
            User::create([
                'name' => 'Developer',
                'email' => 'developer@sisprov.com.br',
                'password' => Hash::make('dev123'),
                'role_id' => $devRole->id,
                'is_active' => true,
            ]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !User::where('email', 'admin@sisprov.com.br')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@sisprov.com.br',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);
        }

        $techRole = Role::where('name', 'technician')->first();
        if ($techRole && !User::where('email', 'tecnico@sisprov.com.br')->exists()) {
            User::create([
                'name' => 'Técnico',
                'email' => 'tecnico@sisprov.com.br',
                'password' => Hash::make('tecnico123'),
                'role_id' => $techRole->id,
                'is_active' => true,
            ]);
        }

        $admRole = Role::where('name', 'administrativo')->first();
        if ($admRole && !User::where('email', 'administrativo@sisprov.com.br')->exists()) {
            User::create([
                'name' => 'Administrativo',
                'email' => 'administrativo@sisprov.com.br',
                'password' => Hash::make('adm123'),
                'role_id' => $admRole->id,
                'is_active' => true,
            ]);
        }
    }
}
