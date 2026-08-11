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
        $devEmail = env('ADMIN_EMAIL', 'developer@sisprov.com.br');
        $devPassword = env('ADMIN_PASSWORD', 'dev123');

        $devRole = Role::where('name', 'developer')->first();
        if ($devRole && !User::where('email', $devEmail)->exists()) {
            User::create([
                'name' => 'Developer',
                'email' => $devEmail,
                'password' => Hash::make($devPassword),
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
