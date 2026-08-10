<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $developer = Role::create(['name' => 'developer', 'label' => 'Developer', 'description' => 'Acesso irrestrito total para manutenção e desenvolvimento', 'is_locked' => true]);
        $admin = Role::create(['name' => 'admin', 'label' => 'Administrador', 'description' => 'Administrador do sistema como cliente', 'is_locked' => true]);
        $technician = Role::create(['name' => 'technician', 'label' => 'Técnico', 'description' => 'Acesso a recursos técnicos e manutenção', 'is_locked' => true]);
        $administrativo = Role::create(['name' => 'administrativo', 'label' => 'Administrativo', 'description' => 'Acesso a serviços administrativos e financeiros', 'is_locked' => true]);
        $client = Role::create(['name' => 'client', 'label' => 'Cliente', 'description' => 'Acesso aos serviços contratados', 'is_locked' => true]);

        $permissions = [
            ['name' => 'dashboard.view', 'label' => 'Ver Dashboard', 'group' => 'Dashboard'],
            ['name' => 'clients.view', 'label' => 'Ver Clientes', 'group' => 'Clientes'],
            ['name' => 'clients.create', 'label' => 'Criar Clientes', 'group' => 'Clientes'],
            ['name' => 'clients.edit', 'label' => 'Editar Clientes', 'group' => 'Clientes'],
            ['name' => 'clients.delete', 'label' => 'Excluir Clientes', 'group' => 'Clientes'],
            ['name' => 'plans.view', 'label' => 'Ver Planos', 'group' => 'Planos'],
            ['name' => 'plans.create', 'label' => 'Criar Planos', 'group' => 'Planos'],
            ['name' => 'plans.edit', 'label' => 'Editar Planos', 'group' => 'Planos'],
            ['name' => 'plans.delete', 'label' => 'Excluir Planos', 'group' => 'Planos'],
            ['name' => 'contracts.view', 'label' => 'Ver Contratos', 'group' => 'Contratos'],
            ['name' => 'contracts.create', 'label' => 'Criar Contratos', 'group' => 'Contratos'],
            ['name' => 'contracts.edit', 'label' => 'Editar Contratos', 'group' => 'Contratos'],
            ['name' => 'contracts.delete', 'label' => 'Excluir Contratos', 'group' => 'Contratos'],
            ['name' => 'servers.view', 'label' => 'Ver Servidores', 'group' => 'Servidores'],
            ['name' => 'servers.create', 'label' => 'Criar Servidores', 'group' => 'Servidores'],
            ['name' => 'servers.edit', 'label' => 'Editar Servidores', 'group' => 'Servidores'],
            ['name' => 'servers.delete', 'label' => 'Excluir Servidores', 'group' => 'Servidores'],
            ['name' => 'links.view', 'label' => 'Ver Links', 'group' => 'Links'],
            ['name' => 'links.create', 'label' => 'Criar Links', 'group' => 'Links'],
            ['name' => 'links.edit', 'label' => 'Editar Links', 'group' => 'Links'],
            ['name' => 'links.delete', 'label' => 'Excluir Links', 'group' => 'Links'],
            ['name' => 'invoices.view', 'label' => 'Ver Faturas', 'group' => 'Faturamento'],
            ['name' => 'invoices.create', 'label' => 'Criar Faturas', 'group' => 'Faturamento'],
            ['name' => 'invoices.edit', 'label' => 'Editar Faturas', 'group' => 'Faturamento'],
            ['name' => 'invoices.delete', 'label' => 'Excluir Faturas', 'group' => 'Faturamento'],
            ['name' => 'payments.register', 'label' => 'Registrar Pagamentos', 'group' => 'Faturamento'],
            ['name' => 'tickets.view', 'label' => 'Ver Chamados', 'group' => 'Chamados'],
            ['name' => 'tickets.create', 'label' => 'Criar Chamados', 'group' => 'Chamados'],
            ['name' => 'tickets.edit', 'label' => 'Editar Chamados', 'group' => 'Chamados'],
            ['name' => 'tickets.manage', 'label' => 'Gerenciar Chamados', 'group' => 'Chamados'],
            ['name' => 'users.view', 'label' => 'Ver Usuários', 'group' => 'Usuários'],
            ['name' => 'users.create', 'label' => 'Criar Usuários', 'group' => 'Usuários'],
            ['name' => 'users.edit', 'label' => 'Editar Usuários', 'group' => 'Usuários'],
            ['name' => 'users.delete', 'label' => 'Excluir Usuários', 'group' => 'Usuários'],
            ['name' => 'reports.view', 'label' => 'Ver Relatórios', 'group' => 'Relatórios'],
            ['name' => 'settings.view', 'label' => 'Ver Configurações', 'group' => 'Configurações'],
            ['name' => 'settings.edit', 'label' => 'Editar Configurações', 'group' => 'Configurações'],
            ['name' => 'audit.view', 'label' => 'Ver Auditoria', 'group' => 'Auditoria'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        $adminPermissionNames = array_column($permissions, 'name');
        $admin->syncPermissions($adminPermissionNames);

        $technicianPermissions = [
            'dashboard.view',
            'clients.view',
            'clients.create', 'clients.edit',
            'plans.view',
            'contracts.view', 'contracts.create', 'contracts.edit',
            'servers.view', 'servers.create', 'servers.edit',
            'links.view', 'links.create', 'links.edit',
            'invoices.view',
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.manage',
            'reports.view',
        ];
        $technician->syncPermissions($technicianPermissions);

        $allPermissionNames = array_column($permissions, 'name');
        $developer->syncPermissions($allPermissionNames);

        $administrativoPermissions = [
            'dashboard.view',
            'clients.view', 'clients.create', 'clients.edit',
            'plans.view',
            'contracts.view', 'contracts.create', 'contracts.edit',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'payments.register',
            'tickets.view', 'tickets.create', 'tickets.edit',
            'reports.view',
            'settings.view',
        ];
        $administrativo->syncPermissions($administrativoPermissions);

        $clientPermissions = [
            'dashboard.view',
            'tickets.view', 'tickets.create',
        ];
        $client->syncPermissions($clientPermissions);
    }
}
