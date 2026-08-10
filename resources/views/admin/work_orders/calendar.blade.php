@extends('layouts.admin')
@section('title', 'OS - Calendário')
@section('page-title', 'OS - Calendário')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <div class="flex rounded border border-gray-300 text-sm overflow-hidden">
        <a href="{{ route('admin.work-orders.index', request()->query()) }}" class="px-3 py-1.5 bg-white text-gray-600 hover:bg-gray-50">Lista</a>
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'kanban'])) }}" class="px-3 py-1.5 bg-white text-gray-600 hover:bg-gray-50">Kanban</a>
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'calendar'])) }}" class="px-3 py-1.5 bg-primary-600 text-white">Calendário</a>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'calendar', 'month' => $prevMonth])) }}" class="text-sm text-primary-600 hover:underline">&larr; Mês anterior</a>
        <span class="text-sm font-semibold text-gray-700">{{ $monthName }} {{ $year }}</span>
        <a href="{{ route('admin.work-orders.index', array_merge(request()->query(), ['view' => 'calendar', 'month' => $nextMonth])) }}" class="text-sm text-primary-600 hover:underline">Próximo mês &rarr;</a>
    </div>
</div>

@php
$daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
$groupedByDate = $orders->groupBy(fn($o) => $o->scheduled_at?->format('Y-m-d'));
@endphp

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                @foreach($daysOfWeek as $day)
                <th class="px-3 py-2 text-center font-medium text-gray-600 text-xs">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($weeks as $week)
            <tr class="border-t">
                @foreach($week as $day)
                @php
                    $dateStr = $day ? $day->format('Y-m-d') : null;
                    $dayOrders = $dateStr && isset($groupedByDate[$dateStr]) ? $groupedByDate[$dateStr] : collect();
                    $isToday = $dateStr && $day->isToday();
                    $isCurrentMonth = $day && $day->month == $month;
                @endphp
                <td class="px-2 py-3 align-top {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }} {{ $isToday ? 'ring-2 ring-primary-400 ring-inset' : '' }} min-h-[80px]">
                    @if($day)
                    <div class="text-xs font-semibold {{ $isCurrentMonth ? 'text-gray-800' : 'text-gray-400' }} mb-1">{{ $day->day }}</div>
                    @if($dayOrders->count() > 0)
                    <a href="{{ route('admin.work-orders.index', ['date' => $dateStr]) }}" class="inline-block bg-primary-100 text-primary-700 text-xs px-2 py-0.5 rounded-full hover:bg-primary-200">
                        {{ $dayOrders->count() }} OS
                    </a>
                    @endif
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
