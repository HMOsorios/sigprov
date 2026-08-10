@extends('layouts.admin')

@section('title', 'Links')
@section('page-title', 'Links')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.links.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="PPPoE, IP, ONT serial, cliente..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inativo</option>
                <option value="blocked" {{ request('status')=='blocked' ? 'selected' : '' }}>Bloqueado</option>
                <option value="maintenance" {{ request('status')=='maintenance' ? 'selected' : '' }}>Manutenção</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Servidor</label>
            <select name="server_id" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                @foreach($servers as $s)
                    <option value="{{ $s->id }}" {{ request('server_id')==$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.links.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $links->total() }} links</p>
        <a href="{{ route('admin.links.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Link</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Usuário PPPoE</th>
                    <th class="p-3 font-medium">Cliente</th>
                    <th class="p-3 font-medium">IP</th>
                    <th class="p-3 font-medium">ONT Serial</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Servidor</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-mono">{{ $link->pppoe_user ?? '-' }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.clients.show', $link->contract->client) }}" class="text-primary-600 hover:underline">
                                {{ $link->contract->client->company_name ?? '-' }}
                            </a>
                        </td>
                        <td class="p-3 font-mono">{{ $link->ip_address ?? '-' }}</td>
                        <td class="p-3 font-mono">{{ $link->ont_serial ?? '-' }}</td>
                        <td class="p-3">
                            @php $sc = $link->status_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $link->status_label }}
                            </span>
                        </td>
                        <td class="p-3">{{ $link->server->name ?? '-' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.links.edit', $link) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.links.show', $link) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.links.destroy', $link) }}" class="inline" onsubmit="return confirm('Excluir este link?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="7">Nenhum link encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $links])
    </div>
</div>
@endsection
