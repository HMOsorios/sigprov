@extends('layouts.admin')

@section('title', 'Relatório de Clientes')
@section('page-title', 'Relatório de Clientes')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Ativos</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $totalActive }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Bloqueados</p>
        <p class="text-2xl font-bold text-red-600">{{ $totalBlocked }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-gray-500">
        <p class="text-sm text-gray-500">Cancelados</p>
        <p class="text-2xl font-bold text-gray-600">{{ $totalCanceled }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribuição por Status</h3>
        <div class="space-y-3">
            @php
                $statusLabels = ['active'=>'Ativo','inactive'=>'Inativo','blocked'=>'Bloqueado','canceled'=>'Cancelado'];
                $statusColors = ['active'=>'emerald','inactive'=>'gray','blocked'=>'red','canceled'=>'gray'];
                $totalAll = $clientStats->sum('total');
            @endphp
            @foreach($clientStats as $stat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $statusLabels[$stat->status] ?? $stat->status }}</span>
                        <span class="font-medium">{{ $stat->total }} ({{ $totalAll > 0 ? round($stat->total / $totalAll * 100) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-{{ $statusColors[$stat->status] ?? 'gray' }}-500 h-2.5 rounded-full" style="width:{{ $totalAll > 0 ? ($stat->total / $totalAll * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Clientes (por faturamento)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">#</th>
                        <th class="pb-2">Cliente</th>
                        <th class="pb-2">Total Faturado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topClients as $i => $client)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $i + 1 }}</td>
                            <td class="py-2">
                                <a href="{{ route('admin.clients.show', $client) }}" class="text-primary-600 hover:underline">{{ $client->company_name }}</a>
                            </td>
                            <td class="py-2 font-medium">R$ {{ number_format($client->invoices_sum_total ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Clientes por Cidade</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Cidade</th>
                    <th class="pb-2">UF</th>
                    <th class="pb-2">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientsByCity as $city)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $city->city }}</td>
                        <td class="py-2">{{ $city->state }}</td>
                        <td class="py-2">{{ $city->total }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum dado encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
