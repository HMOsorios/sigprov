@extends('layouts.admin')

@section('title', 'Inadimplência - Relatórios')
@section('page-title', 'Relatório de Inadimplência')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Total Inadimplente</p>
        <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalOverdue, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">% Inadimplência</p>
        <p class="text-2xl font-bold text-amber-600">{{ $delinquencyPercent }}%</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Carteira</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalPortfolio, 2, ',', '.') }}</p>
    </div>
</div>

@if($delinquencyPercent > 10)
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded shadow-sm flex items-center gap-2">
        <span class="text-lg">⚠️</span>
        <span class="font-medium">Atenção: A inadimplência está acima de 10% ({{ $delinquencyPercent }}%). Recomenda-se ação imediata de cobrança.</span>
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aging de Inadimplência</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Faixa</th>
                    <th class="pb-2">Qtd</th>
                    <th class="pb-2">Valor</th>
                    <th class="pb-2">% do Inadimplente</th>
                </tr>
            </thead>
            <tbody>
                @php $ranges = ['0_30'=>'0-30 dias','31_60'=>'31-60 dias','61_90'=>'61-90 dias','90_plus'=>'90+ dias']; @endphp
                @foreach(['0_30','31_60','61_90','90_plus'] as $key)
                    @php $row = $agingData[$key] ?? ['total'=>0,'amount'=>0]; @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $ranges[$key] }}</td>
                        <td class="py-2">{{ $row['total'] }}</td>
                        <td class="py-2 text-red-600 font-medium">R$ {{ number_format($row['amount'], 2, ',', '.') }}</td>
                        <td class="py-2">{{ $totalOverdue > 0 ? round(($row['amount'] / $totalOverdue) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-semibold border-t-2 border-gray-300">
                    <td class="py-2">Total</td>
                    <td class="py-2">{{ collect($agingData)->sum('total') }}</td>
                    <td class="py-2">R$ {{ number_format($totalOverdue, 2, ',', '.') }}</td>
                    <td class="py-2">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Relatórios</a>
</div>
@endsection
