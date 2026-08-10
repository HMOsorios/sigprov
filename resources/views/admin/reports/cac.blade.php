@extends('layouts.admin')

@section('title', 'CAC - Relatórios')
@section('page-title', 'Relatório de CAC (Custo de Aquisição)')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.cac') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
            <select name="year" class="rounded border-gray-300 border px-3 py-2 text-sm">
                @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Custo Marketing Mensal (R$)</label>
            <input type="number" name="marketing_cost" value="{{ $monthlyMarketingCost }}" class="rounded border-gray-300 border px-3 py-2 text-sm w-40" step="0.01" min="0">
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Novos Clientes</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalNewClients }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">CAC Mensal</p>
        <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format(collect($cacData)->avg('cac') ?: 0, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">CAC Anual</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalNewClients > 0 ? ($monthlyMarketingCost * 12) / $totalNewClients : 0, 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">CAC Mensal</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mês</th>
                    <th class="pb-2">Novos Clientes</th>
                    <th class="pb-2">Custo Marketing</th>
                    <th class="pb-2">CAC</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
                @foreach($cacData as $d)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $months[$d['month'] - 1] }}</td>
                        <td class="py-2">{{ $d['new_clients'] }}</td>
                        <td class="py-2">R$ {{ number_format($d['marketing_cost'], 2, ',', '.') }}</td>
                        <td class="py-2 font-medium">R$ {{ number_format($d['cac'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold border-t-2 border-gray-300">
                    <td class="py-2">Total</td>
                    <td class="py-2">{{ collect($cacData)->sum('new_clients') }}</td>
                    <td class="py-2">R$ {{ number_format(collect($cacData)->sum('marketing_cost'), 2, ',', '.') }}</td>
                    <td class="py-2">R$ {{ number_format(collect($cacData)->avg('cac') ?: 0, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
