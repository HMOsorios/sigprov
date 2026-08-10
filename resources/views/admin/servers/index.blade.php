@extends('layouts.admin')

@section('title', 'Servidores')
@section('page-title', 'Servidores')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.servers.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, hostname, IP..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="router" {{ request('type')=='router' ? 'selected' : '' }}>Roteador</option>
                <option value="switch" {{ request('type')=='switch' ? 'selected' : '' }}>Switch</option>
                <option value="server" {{ request('type')=='server' ? 'selected' : '' }}>Servidor</option>
                <option value="firewall" {{ request('type')=='firewall' ? 'selected' : '' }}>Firewall</option>
                <option value="nas" {{ request('type')=='nas' ? 'selected' : '' }}>NAS</option>
                <option value="radius" {{ request('type')=='radius' ? 'selected' : '' }}>Radius</option>
                <option value="dhcp" {{ request('type')=='dhcp' ? 'selected' : '' }}>DHCP</option>
                <option value="dns" {{ request('type')=='dns' ? 'selected' : '' }}>DNS</option>
                <option value="other" {{ request('type')=='other' ? 'selected' : '' }}>Outro</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="online" {{ request('status')=='online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('status')=='offline' ? 'selected' : '' }}>Offline</option>
                <option value="maintenance" {{ request('status')=='maintenance' ? 'selected' : '' }}>Manutenção</option>
                <option value="error" {{ request('status')=='error' ? 'selected' : '' }}>Erro</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.servers.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $servers->total() }} servidores</p>
        <a href="{{ route('admin.servers.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Servidor</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nome</th>
                    <th class="p-3 font-medium">IP</th>
                    <th class="p-3 font-medium">Tipo</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Marca</th>
                    <th class="p-3 font-medium">Localização</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servers as $server)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $server->name }}</td>
                        <td class="p-3 font-mono">{{ $server->ip_address }}</td>
                        <td class="p-3">{{ $server->type_label }}</td>
                        <td class="p-3">
                            @php $sc = $server->status_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $server->status_label }}
                            </span>
                        </td>
                        <td class="p-3">{{ $server->brand ?? '-' }}</td>
                        <td class="p-3">{{ $server->location ?? '-' }}</td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.servers.ping', $server) }}" class="inline mr-2">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:text-emerald-800 mr-2">Ping</button>
                            </form>
                            <a href="{{ route('admin.servers.edit', $server) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.servers.show', $server) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.servers.destroy', $server) }}" class="inline" onsubmit="return confirm('Excluir este servidor?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="7">Nenhum servidor encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $servers])
    </div>
</div>
@endsection
