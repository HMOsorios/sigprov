@extends('layouts.admin')
@section('title', 'Equipamentos')
@section('page-title', 'Equipamentos')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.equipment.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Serial, patrimônio, marca, modelo, MAC..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($types as $t)
                <option value="{{ $t }}" {{ request('type')==$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="disponivel" {{ request('status')=='disponivel' ? 'selected' : '' }}>Disponível</option>
                <option value="emprestado" {{ request('status')=='emprestado' ? 'selected' : '' }}>Emprestado</option>
                <option value="manutencao" {{ request('status')=='manutencao' ? 'selected' : '' }}>Manutenção</option>
                <option value="descartado" {{ request('status')=='descartado' ? 'selected' : '' }}>Descartado</option>
                <option value="perdido" {{ request('status')=='perdido' ? 'selected' : '' }}>Perdido</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.equipment.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $equipment->total() }} equipamentos</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.equipment.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Equipamento</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">Serial</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Tipo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Marca/Modelo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">MAC</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Cliente</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($equipment as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">{{ $e->serial }}</td>
                    <td class="px-4 py-3">{{ $e->type_label }}</td>
                    <td class="px-4 py-3">{{ $e->brand }} {{ $e->model }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $e->mac ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $e->currentAssignment?->client?->name_display ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded bg-{{ $e->status_color }}-100 text-{{ $e->status_color }}-700">{{ $e->status_label }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.equipment.show', $e) }}" class="text-primary-600 hover:text-primary-800 text-sm">Detalhes</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhum equipamento encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $equipment->links() }}</div>
</div>
@endsection
