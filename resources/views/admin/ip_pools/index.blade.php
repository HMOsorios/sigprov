@extends('layouts.admin')
@section('title', 'Pools de IP')
@section('page-title', 'Pools de IP')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.ip-pools.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, subnet..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="ipv4" {{ request('type')=='ipv4' ? 'selected' : '' }}>IPv4</option>
                <option value="ipv6" {{ request('type')=='ipv6' ? 'selected' : '' }}>IPv6</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.ip-pools.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $pools->total() }} pools</p>
        <a href="{{ route('admin.ip-pools.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Pool</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nome</th>
                    <th class="p-3 font-medium">Subnet</th>
                    <th class="p-3 font-medium">Range</th>
                    <th class="p-3 font-medium">Gateway</th>
                    <th class="p-3 font-medium">Tipo</th>
                    <th class="p-3 font-medium">CGNAT</th>
                    <th class="p-3 font-medium">Utilização</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pools as $pool)
                    @php $usage = $pool->usage_percent; @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $pool->name }}</td>
                        <td class="p-3 font-mono text-xs">{{ $pool->subnet }}</td>
                        <td class="p-3 font-mono text-xs">{{ $pool->range }}</td>
                        <td class="p-3 font-mono text-xs">{{ $pool->gateway }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $pool->type=='ipv4' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ strtoupper($pool->type) }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if($pool->is_cgnat)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-800">CGNAT</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-3 min-w-[160px]">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium w-12 text-right">{{ $pool->used }}/{{ $pool->total }}</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $usage < 70 ? 'bg-emerald-500' : ($usage <= 90 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $usage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-10">{{ $usage }}%</span>
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $pool->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $pool->status=='active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.ip-pools.edit', $pool) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.ip-pools.show', $pool) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.ip-pools.destroy', $pool) }}" class="inline" onsubmit="return confirm('Excluir este pool?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="9">Nenhum pool encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $pools])
    </div>
</div>
@endsection
