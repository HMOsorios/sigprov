@extends('layouts.admin')

@section('title', 'Relatório de Chamados')
@section('page-title', 'Relatório de Chamados')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.tickets') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
            <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
            <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Chamados</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalTickets }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Resolvidos</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $resolvedTickets }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Tempo Médio</p>
        <p class="text-2xl font-bold text-gray-800">{{ $averageResolutionTime ? number_format($averageResolutionTime, 1) : '-' }}h</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">Taxa Resolução</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalTickets > 0 ? round($resolvedTickets / $totalTickets * 100) : 0 }}%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Chamados por Categoria</h3>
        <div class="space-y-3">
            @php
                $catLabels = ['technical'=>'Técnico','billing'=>'Financeiro','commercial'=>'Comercial','installation'=>'Instalação','complaint'=>'Reclamação','other'=>'Outro'];
                $catColors = ['technical'=>'blue','billing'=>'emerald','commercial'=>'violet','installation'=>'amber','complaint'=>'red','other'=>'gray'];
                $totalCat = $ticketsByCategory->sum('total') ?: 1;
            @endphp
            @foreach($ticketsByCategory as $tc)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $catLabels[$tc->category] ?? $tc->category }}</span>
                        <span class="font-medium">{{ $tc->total }} ({{ round($tc->total / $totalCat * 100) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-{{ $catColors[$tc->category] ?? 'gray' }}-500 h-2.5 rounded-full" style="width:{{ $tc->total / $totalCat * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Chamados por Prioridade</h3>
        <div class="space-y-3">
            @php
                $priLabels = ['low'=>'Baixa','medium'=>'Média','high'=>'Alta','critical'=>'Crítica'];
                $priColors = ['low'=>'gray','medium'=>'blue','high'=>'amber','critical'=>'red'];
                $totalPri = $ticketsByPriority->sum('total') ?: 1;
            @endphp
            @foreach($ticketsByPriority as $tp)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $priLabels[$tp->priority] ?? $tp->priority }}</span>
                        <span class="font-medium">{{ $tp->total }} ({{ round($tp->total / $totalPri * 100) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-{{ $priColors[$tp->priority] ?? 'gray' }}-500 h-2.5 rounded-full" style="width:{{ $tp->total / $totalPri * 100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
