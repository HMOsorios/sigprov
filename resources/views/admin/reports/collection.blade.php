@extends('layouts.admin')

@section('title', 'Cobrança - Relatórios')
@section('page-title', 'Relatório de Cobrança')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.collection') }}" class="flex items-center gap-3">
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
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Total Recebido</p>
        <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($totalCollected, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Total em Aberto</p>
        <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalOutstanding, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Taxa de Cobrança</p>
        <p class="text-2xl font-bold {{ $overallRate >= 90 ? 'text-emerald-600' : ($overallRate >= 70 ? 'text-amber-600' : 'text-red-600') }}">{{ $overallRate }}%</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cobrança Mensal</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Mês</th>
                    <th class="pb-2">Total Faturado</th>
                    <th class="pb-2">Recebido</th>
                    <th class="pb-2">Em Aberto</th>
                    <th class="pb-2">% Recebido</th>
                </tr>
            </thead>
            <tbody>
                @php $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
                @foreach($collectionData as $d)
                    @php
                        $rateColor = $d['rate'] >= 90 ? 'text-emerald-600' : ($d['rate'] >= 70 ? 'text-amber-600' : 'text-red-600');
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $months[$d['month'] - 1] }}</td>
                        <td class="py-2">R$ {{ number_format($d['total'], 2, ',', '.') }}</td>
                        <td class="py-2 text-emerald-600 font-medium">R$ {{ number_format($d['collected'], 2, ',', '.') }}</td>
                        <td class="py-2 text-red-600 font-medium">R$ {{ number_format($d['outstanding'], 2, ',', '.') }}</td>
                        <td class="py-2 font-medium {{ $rateColor }}">{{ $d['rate'] }}%</td>
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
