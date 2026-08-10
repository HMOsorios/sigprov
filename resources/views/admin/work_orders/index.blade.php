@extends('layouts.admin')
@section('title', 'Ordens de Serviço')
@section('page-title', 'Ordens de Serviço')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.work-orders.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="OS #, cliente, descrição..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pendente</option>
                <option value="scheduled" {{ request('status')=='scheduled' ? 'selected' : '' }}>Agendado</option>
                <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>Em Andamento</option>
                <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Concluído</option>
                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="install" {{ request('type')=='install' ? 'selected' : '' }}>Instalação</option>
                <option value="maintenance" {{ request('type')=='maintenance' ? 'selected' : '' }}>Manutenção</option>
                <option value="repair" {{ request('type')=='repair' ? 'selected' : '' }}>Reparo</option>
                <option value="remove" {{ request('type')=='remove' ? 'selected' : '' }}>Retirada</option>
                <option value="visit" {{ request('type')=='visit' ? 'selected' : '' }}>Visita</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Técnico</label>
            <select name="technician_id" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($technicians as $tech)
                <option value="{{ $tech->id }}" {{ request('technician_id')==$tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
            <select name="priority" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todas</option>
                <option value="low" {{ request('priority')=='low' ? 'selected' : '' }}>Baixa</option>
                <option value="medium" {{ request('priority')=='medium' ? 'selected' : '' }}>Média</option>
                <option value="high" {{ request('priority')=='high' ? 'selected' : '' }}>Alta</option>
                <option value="critical" {{ request('priority')=='critical' ? 'selected' : '' }}>Crítica</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.work-orders.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600">Total: {{ $orders->total() }} OS</p>
        <div class="flex items-center gap-3">
            <div class="flex rounded border border-gray-300 text-sm overflow-hidden">
                <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'list'])) }}" class="px-3 py-1.5 {{ request('view', 'list')=='list' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Lista</a>
                <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'kanban'])) }}" class="px-3 py-1.5 {{ request('view')=='kanban' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Kanban</a>
                <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'calendar'])) }}" class="px-3 py-1.5 {{ request('view')=='calendar' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Calendário</a>
            </div>
            <a href="{{ route('admin.work-orders.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Nova OS</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">#</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Tipo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Cliente</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Técnico</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Prioridade</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Agendado para</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $order->id }}</td>
                    <td class="px-4 py-3">{{ $order->type_label }}</td>
                    <td class="px-4 py-3">{{ $order->client?->name_display ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $order->technician?->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $pc = match($order->priority) {
                                'critical' => 'bg-red-100 text-red-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'medium' => 'bg-blue-100 text-blue-800',
                                'low' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $pc }}">{{ $order->priority_label }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $order->status_color=='success' ? 'bg-emerald-100 text-emerald-800' : ($order->status_color=='warning' ? 'bg-amber-100 text-amber-800' : ($order->status_color=='danger' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs">{{ $order->scheduled_at ? $order->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.work-orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 text-sm">Ver</a>
                        <a href="{{ route('admin.work-orders.edit', $order) }}" class="text-amber-600 hover:text-amber-800 text-sm">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Nenhuma OS encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $orders->links() }}</div>
</div>
@endsection
