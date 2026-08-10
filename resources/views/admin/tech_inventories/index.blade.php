@extends('layouts.admin')
@section('title', 'Inventário de Técnicos')
@section('page-title', 'Inventário de Técnicos')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.tech-inventories.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Técnico</label>
            <select name="technician_id" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($technicians as $t)
                <option value="{{ $t->id }}" {{ request('technician_id')==$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="checked_out" {{ request('status')=='checked_out' ? 'selected' : '' }}>Retirado</option>
                <option value="checked_in" {{ request('status')=='checked_in' ? 'selected' : '' }}>Devolvido</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.tech-inventories.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $inventories->total() }} registros</p>
        <a href="{{ route('admin.tech-inventories.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Nova Retirada</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">Técnico</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Equipamento</th>
                    <th class="px-4 py-3 font-medium text-gray-600">OS #</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Retirado em</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Devolvido em</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Condições</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($inventories as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $inv->technician?->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs">{{ $inv->equipment?->serial ?? '-' }}</span>
                        <span class="text-gray-500"> - {{ $inv->equipment?->model ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $inv->work_order_id ? '#'.$inv->work_order_id : '-' }}</td>
                    <td class="px-4 py-3 text-xs">{{ $inv->checked_out_at ? $inv->checked_out_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-3 text-xs">{{ $inv->checked_in_at ? $inv->checked_in_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs">{{ $inv->condition_out ?? '-' }}</span>
                        @if($inv->condition_in)
                        <span class="text-xs text-gray-400"> &rarr; {{ $inv->condition_in }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if(!$inv->checked_in_at)
                        <form method="POST" action="{{ route('admin.tech-inventories.checkin', $inv) }}">
                            @csrf
                            <button type="submit" class="bg-emerald-600 text-white px-3 py-1 rounded text-xs hover:bg-emerald-700">Registrar Devolução</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $inventories->links() }}</div>
</div>
@endsection
