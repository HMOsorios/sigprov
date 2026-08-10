@extends('layouts.admin')

@section('title', 'Relatório Financeiro')
@section('page-title', 'Relatório Financeiro')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Receita Total ({{ $year }})</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Pendente</p>
        <p class="text-2xl font-bold text-amber-600">R$ {{ number_format($totalPending, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Ticket Médio</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($averageTicket ?? 0, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">Faturado ({{ $year }})</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($invoicedTotal, 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Receita Mensal ({{ $year }})</h3>
        <form method="GET" action="{{ route('admin.reports.financial') }}" class="flex items-center gap-2">
            <select name="year" class="rounded border-gray-300 border px-3 py-2 text-sm">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mês</th>
                    <th class="pb-2">Recebido</th>
                    <th class="pb-2">Pendente</th>
                    <th class="pb-2">Qtd. Recebido</th>
                    <th class="pb-2">Qtd. Pendente</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
                @foreach($monthlyRevenue as $mr)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $months[$mr->month - 1] }}</td>
                        <td class="py-2 text-emerald-600 font-medium">R$ {{ number_format($mr->paid, 2, ',', '.') }}</td>
                        <td class="py-2 text-amber-600 font-medium">R$ {{ number_format($mr->pending, 2, ',', '.') }}</td>
                        <td class="py-2">{{ $mr->paid_count }}</td>
                        <td class="py-2">{{ $mr->pending_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Gráfico de Receita Mensal</h3>
    @php $maxVal = max($monthlyRevenue->max('paid'), $monthlyRevenue->max('pending'), 1); @endphp
    <div class="flex items-end gap-2" style="height:250px">
        @foreach($monthlyRevenue as $mr)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full flex gap-0.5 items-end" style="height:220px">
                    <div class="flex-1 bg-emerald-500 rounded-t" style="height:{{ max(($mr->paid / $maxVal) * 200, 2) }}px" title="Recebido: R$ {{ number_format($mr->paid, 2, ',', '.') }}"></div>
                    <div class="flex-1 bg-amber-500 rounded-t" style="height:{{ max(($mr->pending / $maxVal) * 200, 2) }}px" title="Pendente: R$ {{ number_format($mr->pending, 2, ',', '.') }}"></div>
                </div>
                <span class="text-xs text-gray-500 mt-1">{{ $months[$mr->month - 1] }}</span>
            </div>
        @endforeach
    </div>
    <div class="flex items-center gap-4 mt-4 text-sm">
        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-emerald-500"></div> Recebido</div>
        <div class="flex items-center gap-1"><div class="w-3 h-3 rounded bg-amber-500"></div> Pendente</div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
