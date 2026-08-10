@php
$currentRoute = Route::currentRouteName();
$navItems = [
    ['name' => 'dashboard', 'label' => 'Dashboard', 'icon' => '📊', 'route' => 'admin.dashboard'],
    'Gestão' => [
        ['name' => 'clients', 'label' => 'Clientes', 'icon' => '👥', 'route' => 'admin.clients.index'],
        ['name' => 'leads', 'label' => 'Leads', 'icon' => '🎯', 'route' => 'admin.leads.index', 'roles' => ['developer', 'admin']],
        ['name' => 'plans', 'label' => 'Planos', 'icon' => '📋', 'route' => 'admin.plans.index'],
        ['name' => 'contracts', 'label' => 'Contratos', 'icon' => '📄', 'route' => 'admin.contracts.index'],
    ],
    'Infraestrutura' => [
        ['name' => 'servers', 'label' => 'Servidores', 'icon' => '🖥️', 'route' => 'admin.servers.index', 'roles' => ['developer', 'admin', 'technician']],
        ['name' => 'links', 'label' => 'Links', 'icon' => '🔗', 'route' => 'admin.links.index', 'roles' => ['developer', 'admin', 'technician']],
    ],
    'Financeiro' => [
        ['name' => 'invoices', 'label' => 'Faturas', 'icon' => '💰', 'route' => 'admin.invoices.index'],
    ],
    'Suporte' => [
        ['name' => 'tickets', 'label' => 'Chamados', 'icon' => '🎫', 'route' => 'admin.tickets.index'],
    ],
    'Relatórios' => [
        ['name' => 'reports', 'label' => 'Relatórios', 'icon' => '📈', 'route' => 'admin.reports.index'],
    ],
    'Sistema' => [
        ['name' => 'users', 'label' => 'Usuários', 'icon' => '🔐', 'route' => 'admin.users.index', 'roles' => ['developer', 'admin']],
        ['name' => 'settings', 'label' => 'Configurações', 'icon' => '⚙️', 'route' => 'admin.settings.index'],
    ],
];
@endphp
<aside class="w-64 bg-slate-900 text-white flex flex-col sidebar-transition">
    <div class="p-4 border-b border-slate-700">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <span class="text-2xl">🌐</span>
            <span class="font-bold text-lg">SisProv</span>
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <span>📊</span> Dashboard
        </a>

        @foreach($navItems as $group => $items)
            @if(is_array($items) && !isset($items['name']))
                <div class="pt-3 pb-1">
                    <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $group }}</p>
                </div>
                @foreach($items as $item)
                    @php
                        $requiredRoles = $item['roles'] ?? ($item['role'] ?? null);
                    @endphp
                    @if($requiredRoles && !auth()->user()->hasRole($requiredRoles))
                        @continue
                    @endif
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ str_starts_with($currentRoute, 'admin.' . $item['name']) ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <span>{{ $item['icon'] }}</span> {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
        @endforeach
    </nav>
    <div class="p-3 border-t border-slate-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-red-600 hover:text-white w-full transition">
                <span>🚪</span> Sair
            </button>
        </form>
    </div>
</aside>
