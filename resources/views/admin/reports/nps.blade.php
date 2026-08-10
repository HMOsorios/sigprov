@extends('layouts.admin')

@section('title', 'NPS - Relatórios')
@section('page-title', 'Relatório de NPS (Net Promoter Score)')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-8 flex flex-col items-center justify-center">
        <p class="text-sm text-gray-500 mb-2">NPS Score</p>
        <p class="text-6xl font-extrabold {{ $npsScore >= 50 ? 'text-emerald-600' : ($npsScore >= 0 ? 'text-amber-600' : 'text-red-600') }}">
            {{ $npsScore }}
        </p>
        <p class="text-sm mt-2 {{ $npsScore >= 50 ? 'text-emerald-600' : ($npsScore >= 0 ? 'text-amber-600' : 'text-red-600') }}">
            {{ $npsScore >= 50 ? 'Excelente' : ($npsScore >= 0 ? 'Aperfeiçoável' : 'Crítico') }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Classificação</h3>
        @php
            $total = $promoters + $neutrals + $detractors ?: 1;
            $pctPromoters = ($promoters / $total) * 100;
            $pctNeutrals = ($neutrals / $total) * 100;
            $pctDetractors = ($detractors / $total) * 100;
        @endphp
        <div class="flex h-8 rounded-full overflow-hidden mb-4">
            <div class="bg-emerald-500" style="width:{{ $pctPromoters }}%" title="Promotores: {{ $promoters }}"></div>
            <div class="bg-gray-300" style="width:{{ $pctNeutrals }}%" title="Neutros: {{ $neutrals }}"></div>
            <div class="bg-red-500" style="width:{{ $pctDetractors }}%" title="Detratores: {{ $detractors }}"></div>
        </div>
        <div class="flex justify-between text-sm">
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-emerald-500"></div>
                <span>Promotores: {{ $promoters }} ({{ round($pctPromoters) }}%)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-gray-300"></div>
                <span>Neutros: {{ $neutrals }} ({{ round($pctNeutrals) }}%)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-red-500"></div>
                <span>Detratores: {{ $detractors }} ({{ round($pctDetractors) }}%)</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Promotores</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $promoters }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-gray-400">
        <p class="text-sm text-gray-500">Neutros</p>
        <p class="text-2xl font-bold text-gray-600">{{ $neutrals }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Detratores</p>
        <p class="text-2xl font-bold text-red-600">{{ $detractors }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Respostas</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalNpsResponses }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Fórmula NPS</h3>
    <p class="text-sm text-gray-600 leading-relaxed">
        O NPS é calculado pela fórmula: <strong>% Promotores - % Detratores</strong>.<br>
        <strong>Promotores</strong> (nota 9-10): Clientes satisfeitos que indicariam a empresa.<br>
        <strong>Neutros</strong> (nota 7-8): Clientes satisfeitos, mas não entusiasmados.<br>
        <strong>Detratores</strong> (nota 0-6): Clientes insatisfeitos que podem prejudicar a marca.<br>
        O resultado varia de <strong>-100 a +100</strong>. Acima de 50 é considerado <strong class="text-emerald-600">excelente</strong>,
        entre 0 e 49 é <strong class="text-amber-600">aperfeiçoável</strong>, e abaixo de 0 é <strong class="text-red-600">crítico</strong>.
    </p>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
