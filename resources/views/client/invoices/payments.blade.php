@extends('layouts.client')
@section('title', 'Histórico de Pagamentos')
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b">
        <p class="text-sm text-gray-600">Seus pagamentos realizados</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Fatura</th>
                    <th class="p-3 font-medium">Competência</th>
                    <th class="p-3 font-medium">Valor</th>
                    <th class="p-3 font-medium">Pagamento</th>
                    <th class="p-3 font-medium">Forma</th>
                    <th class="p-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $invoice->invoice_number }}</td>
                    <td class="p-3">{{ $invoice->due_date->format('m/Y') }}</td>
                    <td class="p-3">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                    <td class="p-3">{{ $invoice->paid_at?->format('d/m/Y') ?? '-' }}</td>
                    <td class="p-3">{{ $invoice->payment_method ?? '-' }}</td>
                    <td class="p-3">
                        <a href="{{ route('client.invoices.show', $invoice) }}" class="text-primary-600 hover:underline text-sm">Detalhes</a>
                    </td>
                </tr>
                @empty
                <tr><td class="p-6 text-center text-gray-400" colspan="6">Nenhum pagamento encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
