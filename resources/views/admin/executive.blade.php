@extends('layouts.admin')

@section('title', 'Dashboard Executivo')
@section('page-title', 'Dashboard Executivo')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Receita do Mês</p>
        <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($kpis['revenueThisMonth'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Faturamento</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($kpis['totalInvoiced'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Inadimplência</p>
        <p class="text-2xl font-bold text-red-600">{{ $kpis['delinquencyRate'] }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Churn</p>
        <p class="text-2xl font-bold text-amber-600">{{ $kpis['churnRate'] }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">Contratos Ativos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $kpis['activeContracts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-sky-500">
        <p class="text-sm text-gray-500">Novos Clientes</p>
        <p class="text-2xl font-bold text-sky-600">{{ $kpis['newClients'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Chamados Abertos</p>
        <p class="text-2xl font-bold text-blue-600">{{ $kpis['openTickets'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500">OS Pendentes</p>
        <p class="text-2xl font-bold text-orange-600">{{ $kpis['pendingWorkOrders'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Receita 12 Meses</h3>
        @php
            $maxVal = max($kpis['revenue12Months']->max('paid'), 1);
            $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        @endphp
        <div class="flex items-end gap-2" style="height:220px">
            @foreach($kpis['revenue12Months'] as $r)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-emerald-100 rounded-t" style="height:{{ max(($r->paid / $maxVal) * 200, 4) }}px" title="R$ {{ number_format($r->paid, 2, ',', '.') }}">
                        <div class="w-full bg-emerald-500 rounded-t" style="height:100%"></div>
                    </div>
                    <span class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($r->month . '-01')->format('M') }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-1 mt-2 text-xs text-gray-500">
            <div class="w-3 h-3 rounded bg-emerald-500"></div>
            <span>Recebido</span>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Evolução de Clientes</h3>
        <div class="space-y-3">
            @php $maxClients = max($kpis['clientEvolution']->max('total'), 1); @endphp
            @foreach($kpis['clientEvolution'] as $ce)
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 w-16">{{ \Carbon\Carbon::parse($ce->month . '-01')->format('M/y') }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        <div class="bg-primary-500 h-4 rounded-full" style="width:{{ ($ce->total / $maxClients) * 100 }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-gray-700 w-8 text-right">{{ $ce->total }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Chamados por Status</h3>
    <div class="flex flex-wrap gap-2">
        @php
            $statusLabels = ['open'=>'Aberto','in_progress'=>'Em Andamento','resolved'=>'Resolvido','closed'=>'Fechado','pending'=>'Pendente'];
            $statusColors = ['open'=>'bg-blue-100 text-blue-800','in_progress'=>'bg-amber-100 text-amber-800','resolved'=>'bg-emerald-100 text-emerald-800','closed'=>'bg-gray-100 text-gray-800','pending'=>'bg-violet-100 text-violet-800'];
        @endphp
        @foreach($kpis['ticketsByStatus'] as $status => $count)
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$status] ?? $status }}: {{ $count }}
            </span>
        @endforeach
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Dashboard</a>
</div>
@endsection
