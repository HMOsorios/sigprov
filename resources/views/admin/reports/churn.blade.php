@extends('layouts.admin')

@section('title', 'Churn - Relatórios')
@section('page-title', 'Relatório de Churn')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.churn') }}" class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700">Ano:</label>
        <select name="year" class="rounded border-gray-300 border px-3 py-2 text-sm">
            @for($y = now()->year; $y >= 2024; $y--)
                <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Churn Rate (%)</p>
        <p class="text-2xl font-bold text-red-600">{{ $churnRate }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Cancelados ({{ $year }})</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalCanceledYear }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Média Contratos Ativos</p>
        <p class="text-2xl font-bold text-gray-800">{{ collect($churnData)->avg('active') ? round(collect($churnData)->avg('active')) : 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Churn Mensal</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mês</th>
                    <th class="pb-2">Ativos</th>
                    <th class="pb-2">Cancelados</th>
                    <th class="pb-2">Churn Rate %</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
                @foreach($churnData as $d)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $months[$d['month'] - 1] }}</td>
                        <td class="py-2">{{ $d['active'] }}</td>
                        <td class="py-2 text-red-600 font-medium">{{ $d['canceled'] }}</td>
                        <td class="py-2">{{ $d['rate'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Taxa de Churn Mensal</h3>
    @php $maxRate = max(collect($churnData)->max('rate'), 1); @endphp
    <div class="flex items-end gap-2" style="height:200px">
        @foreach($churnData as $d)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full bg-red-100 rounded-t" style="height:{{ max(($d['rate'] / $maxRate) * 180, 4) }}px">
                    <div class="w-full bg-red-500 rounded-t" style="height:100%"></div>
                </div>
                <span class="text-xs text-gray-500 mt-1">{{ $months[$d['month'] - 1] }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
