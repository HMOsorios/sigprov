@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-primary-500">
        <p class="text-sm text-gray-500">Total Clientes</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_clients'] }}</p>
        <p class="text-xs text-gray-400">{{ $stats['active_clients'] }} ativos</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Contratos Ativos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_contracts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Links Ativos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_links'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">Servidores</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_servers'] }}</p>
        <p class="text-xs text-emerald-600">{{ $stats['online_servers'] }} online</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Chamados Abertos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['open_tickets'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Faturas Vencidas</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['overdue_invoices'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Receita do Mês</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Receita Pendente</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($stats['pending_revenue'], 2, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Receita Mensal ({{ now()->year }})</h3>
        <div class="flex items-end gap-2" style="height:200px">
            @php $maxRevenue = max($revenue_chart->max(), 1); @endphp
            @foreach(range(1, 12) as $m)
                @php
                    $val = $revenue_chart->get($m, 0);
                    $height = max(($val / $maxRevenue) * 180, 4);
                    $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-primary-100 rounded-t relative" style="height:{{ $height }}px">
                        <div class="w-full bg-primary-500 rounded-t" style="height:100%"></div>
                    </div>
                    <span class="text-xs text-gray-500 mt-1">{{ $months[$m-1] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Chamados por Categoria</h3>
        <div class="space-y-3">
            @foreach($tickets_by_category as $cat => $count)
                @php
                    $colors = ['technical'=>'blue','billing'=>'emerald','commercial'=>'violet','installation'=>'amber','complaint'=>'red','other'=>'gray'];
                    $labels = ['technical'=>'Técnico','billing'=>'Financeiro','commercial'=>'Comercial','installation'=>'Instalação','complaint'=>'Reclamação','other'=>'Outro'];
                @endphp
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $labels[$cat] ?? $cat }}</span>
                    <span class="text-sm font-semibold">{{ $count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-{{ $colors[$cat] ?? 'gray' }}-500 h-2 rounded-full" style="width:{{ $count > 0 ? ($count / $tickets_by_category->sum() * 100) : 0 }}%"></div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Últimos Clientes</h3>
            <a href="{{ route('admin.clients.index') }}" class="text-sm text-primary-600 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Nome</th>
                        <th class="pb-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_clients as $client)
                        <tr class="border-b last:border-0">
                            <td class="py-2">
                                <a href="{{ route('admin.clients.show', $client) }}" class="text-primary-600 hover:underline">{{ $client->company_name }}</a>
                            </td>
                            <td class="py-2">
                                @php $st = $client->status; @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $st=='active' ? 'bg-emerald-100 text-emerald-800' : ($st=='inactive' ? 'bg-gray-100 text-gray-800' : ($st=='blocked' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">
                                    {{ $st=='active' ? 'Ativo' : ($st=='inactive' ? 'Inativo' : ($st=='blocked' ? 'Bloqueado' : 'Cancelado')) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="2">Nenhum cliente</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Chamados Abertos</h3>
            <a href="{{ route('admin.tickets.index') }}" class="text-sm text-primary-600 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Assunto</th>
                        <th class="pb-2">Prioridade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_tickets as $ticket)
                        <tr class="border-b last:border-0">
                            <td class="py-2">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-primary-600 hover:underline">{{ $ticket->subject }}</a>
                            </td>
                            <td class="py-2">
                                @php $p = $ticket->priority; @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $p=='critical' ? 'bg-red-100 text-red-800' : ($p=='high' ? 'bg-amber-100 text-amber-800' : ($p=='medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="2">Nenhum chamado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Faturas Vencidas</h3>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-primary-600 hover:underline">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Fatura</th>
                        <th class="pb-2">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overdue_invoices as $invoice)
                        <tr class="border-b last:border-0">
                            <td class="py-2">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-primary-600 hover:underline">{{ $invoice->invoice_number }}</a>
                            </td>
                            <td class="py-2 font-medium text-red-600">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="2">Nenhuma fatura vencida</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
