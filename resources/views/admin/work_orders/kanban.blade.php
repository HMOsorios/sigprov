@extends('layouts.admin')
@section('title', 'OS - Kanban')
@section('page-title', 'OS - Kanban')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <div class="flex rounded border border-gray-300 text-sm overflow-hidden">
        <a href="{{ route('admin.work-orders.index', request()->query()) }}" class="px-3 py-1.5 bg-white text-gray-600 hover:bg-gray-50">Lista</a>
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'kanban'])) }}" class="px-3 py-1.5 bg-primary-600 text-white">Kanban</a>
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'calendar'])) }}" class="px-3 py-1.5 bg-white text-gray-600 hover:bg-gray-50">Calendário</a>
    </div>
    <a href="{{ route('admin.work-orders.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Nova OS</a>
</div>

@php
$statuses = ['pending' => 'Pendente', 'scheduled' => 'Agendado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'canceled' => 'Cancelado'];
$headerColors = ['pending' => 'bg-gray-500', 'scheduled' => 'bg-blue-500', 'in_progress' => 'bg-amber-500', 'completed' => 'bg-emerald-500', 'canceled' => 'bg-red-500'];
@endphp

<div class="flex gap-4 overflow-x-auto pb-4">
    @foreach($statuses as $key => $label)
    @php $col = $grouped[$key] ?? collect(); @endphp
    <div class="flex-1 min-w-[260px] bg-gray-50 rounded-lg shadow-sm">
        <div class="{{ $headerColors[$key] }} text-white px-4 py-2 rounded-t-lg font-semibold text-sm flex items-center justify-between">
            <span>{{ $label }}</span>
            <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ $col->count() }}</span>
        </div>
        <div class="p-3 space-y-3 min-h-[200px]">
            @forelse($col as $order)
            <a href="{{ route('admin.work-orders.show', $order) }}" class="block bg-white rounded border border-gray-200 p-3 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-1">
                    <span class="font-semibold text-sm">#{{ $order->id }}</span>
                    @php
                        $dotColor = match($order->priority) {
                            'critical' => 'bg-red-500',
                            'high' => 'bg-orange-500',
                            'medium' => 'bg-blue-500',
                            'low' => 'bg-gray-400',
                            default => 'bg-gray-400'
                        };
                    @endphp
                    <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                </div>
                <p class="text-xs text-gray-500 truncate">{{ $order->client?->name_display ?? '-' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">{{ $order->type_label }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1 truncate">{{ Str::limit($order->description, 60) }}</p>
                @if($order->scheduled_at)
                <p class="text-xs text-gray-400 mt-1">{{ $order->scheduled_at->format('d/m/Y H:i') }}</p>
                @endif
            </a>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">Nenhuma OS</p>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
