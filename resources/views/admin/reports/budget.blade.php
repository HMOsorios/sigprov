@extends('layouts.admin')

@section('title', 'Orçamento - Relatórios')
@section('page-title', 'Relatório de Orçamento')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.budget') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
            <select name="year" class="rounded border-gray-300 border px-3 py-2 text-sm">
                @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Mensal (R$)</label>
            <input type="number" name="monthly_goal" value="{{ $monthlyGoal }}" class="rounded border-gray-300 border px-3 py-2 text-sm w-40" step="0.01" min="0">
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Meta Total</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalGoal, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Total Recebido</p>
        <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($totalPaid, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">% Atingimento</p>
        <p class="text-2xl font-bold {{ $totalGoal > 0 ? ($totalPaid / $totalGoal >= 0.9 ? 'text-emerald-600' : ($totalPaid / $totalGoal >= 0.7 ? 'text-amber-600' : 'text-red-600')) : 'text-gray-800' }}">
            {{ $totalGoal > 0 ? round(($totalPaid / $totalGoal) * 100, 1) : 0 }}%
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Total Faturado</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalInvoiced, 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Acompanhamento Mensal</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mês</th>
                    <th class="pb-2">Meta</th>
                    <th class="pb-2">Faturado</th>
                    <th class="pb-2">Recebido</th>
                    <th class="pb-2">% Atingimento</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
                @foreach($budgetData as $d)
                    @php
                        $pct = $d['achievement'];
                        $barColor = $pct >= 90 ? 'bg-emerald-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-red-500');
                        $textColor = $pct >= 90 ? 'text-emerald-600' : ($pct >= 70 ? 'text-amber-600' : 'text-red-600');
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $months[$d['month'] - 1] }}</td>
                        <td class="py-2">R$ {{ number_format($d['goal'], 2, ',', '.') }}</td>
                        <td class="py-2">R$ {{ number_format($d['invoiced'], 2, ',', '.') }}</td>
                        <td class="py-2">R$ {{ number_format($d['paid'], 2, ',', '.') }}</td>
                        <td class="py-2">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-100 rounded-full h-2.5">
                                    <div class="{{ $barColor }} h-2.5 rounded-full" style="width:{{ min($pct, 100) }}%"></div>
                                </div>
                                <span class="{{ $textColor }} font-medium">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
