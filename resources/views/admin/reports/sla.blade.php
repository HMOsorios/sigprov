@extends('layouts.admin')

@section('title', 'SLA - Relatórios')
@section('page-title', 'Relatório de SLA')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.reports.sla') }}" class="flex flex-wrap gap-3 items-end">
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

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Tempo Médio Resolução</p>
        <p class="text-2xl font-bold text-gray-800">{{ $avgFirstResponse ? number_format($avgFirstResponse, 1) : '-' }}h</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Dentro do SLA</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $ticketsWithinSla }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-violet-500">
        <p class="text-sm text-gray-500">SLA %</p>
        <p class="text-2xl font-bold {{ $totalResolved > 0 ? ($ticketsWithinSla / $totalResolved >= 0.9 ? 'text-emerald-600' : ($ticketsWithinSla / $totalResolved >= 0.7 ? 'text-amber-600' : 'text-red-600')) : 'text-gray-800' }}">
            {{ $totalResolved > 0 ? round(($ticketsWithinSla / $totalResolved) * 100, 1) : 0 }}%
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">SLA por Categoria</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Categoria</th>
                        <th class="pb-2">Média (horas)</th>
                        <th class="pb-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $catLabels = ['technical'=>'Técnico','billing'=>'Financeiro','commercial'=>'Comercial','installation'=>'Instalação','complaint'=>'Reclamação','other'=>'Outro']; @endphp
                    @forelse($slaByCategory as $sla)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 font-medium">{{ $catLabels[$sla->category] ?? $sla->category }}</td>
                            <td class="py-2">{{ number_format($sla->avg_hours, 1) }}h</td>
                            <td class="py-2">{{ $sla->total }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum dado encontrado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">SLA por Técnico</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Técnico</th>
                        <th class="pb-2">Média (horas)</th>
                        <th class="pb-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slaByTechnician as $sla)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 font-medium">{{ $sla->assignedTo?->name ?? 'N/A' }}</td>
                            <td class="py-2">{{ number_format($sla->avg_hours, 1) }}h</td>
                            <td class="py-2">{{ $sla->total }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum dado encontrado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
