@extends('layouts.client')
@section('title', 'Meus Contratos')
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Contrato</th>
                    <th class="p-3 font-medium">Plano</th>
                    <th class="p-3 font-medium">Valor</th>
                    <th class="p-3 font-medium">Início</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Assinatura</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $contract->contract_number }}</td>
                    <td class="p-3">{{ $contract->plan?->name ?? '-' }}</td>
                    <td class="p-3">R$ {{ number_format($contract->effective_price, 2, ',', '.') }}</td>
                    <td class="p-3">{{ $contract->start_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $contract->status_label == 'Ativo' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $contract->status_label }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($contract->signature_status === 'signed')
                        <span class="text-emerald-600 text-xs">Assinado</span>
                        @elseif($contract->signature_status === 'sent')
                        <span class="text-amber-600 text-xs">Pendente</span>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-3 text-right space-x-2">
                        @if($contract->status === 'active')
                        <a href="{{ route('client.contracts.cancel', $contract) }}" class="text-red-600 hover:text-red-800 text-xs">Cancelar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td class="p-6 text-center text-gray-400" colspan="7">Nenhum contrato encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
