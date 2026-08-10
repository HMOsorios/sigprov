@extends('layouts.admin')

@section('title', 'Fatura '.$invoice->invoice_number)
@section('page-title', 'Fatura '.$invoice->invoice_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalhes da Fatura</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nº Fatura</dt><dd class="font-medium font-mono">{{ $invoice->invoice_number }}</dd></div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd>
                    @php $sc = $invoice->status_color; @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $invoice->status_label }}
                    </span>
                </dd>
            </div>
            <div><dt class="text-gray-500">Emissão</dt><dd class="font-medium">{{ $invoice->issue_date->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-500">Vencimento</dt><dd class="font-medium">{{ $invoice->due_date->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-500">Valor</dt><dd class="font-medium">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Desconto</dt><dd class="font-medium">R$ {{ number_format($invoice->discount, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Multa</dt><dd class="font-medium">R$ {{ number_format($invoice->late_fee ?? 0, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Juros</dt><dd class="font-medium">R$ {{ number_format($invoice->interest ?? 0, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">Total</dt><dd class="font-semibold text-lg">R$ {{ number_format($invoice->total, 2, ',', '.') }}</dd></div>
            @if($invoice->paid_date)
                <div><dt class="text-gray-500">Data Pagamento</dt><dd class="font-medium">{{ $invoice->paid_date->format('d/m/Y') }}</dd></div>
            @endif
            @if($invoice->notes)
                <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $invoice->notes }}</dd></div>
            @endif
        </dl>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Cliente</h4>
            <p class="font-medium text-sm">{{ $invoice->client->company_name }}</p>
            <p class="text-sm text-gray-500">{{ $invoice->client->document_formatted }}</p>
            <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
            <p class="text-sm text-gray-500">{{ $invoice->client->phone }}</p>
            <a href="{{ route('admin.clients.show', $invoice->client) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Cliente</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Contrato</h4>
            @if($invoice->contract)
                <p class="text-sm font-medium">{{ $invoice->contract->contract_number }}</p>
                <p class="text-sm text-gray-500">{{ $invoice->contract->plan->name ?? '-' }}</p>
                <a href="{{ route('admin.contracts.show', $invoice->contract) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Contrato</a>
            @else
                <p class="text-sm text-gray-400">Nenhum contrato vinculado</p>
            @endif
        </div>

        @if($invoice->boleto_barcode || $invoice->pix_code)
            <div class="bg-white rounded-lg shadow-sm p-5">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Informações de Pagamento</h4>
                @if($invoice->boleto_barcode)
                    <div class="mb-3">
                        <p class="text-xs text-gray-500">Boleto</p>
                        <p class="font-mono text-xs">{{ $invoice->boleto_barcode }}</p>
                        @if($invoice->boleto_url)
                            <a href="{{ $invoice->boleto_url }}" target="_blank" class="text-primary-600 text-xs hover:underline">Visualizar Boleto</a>
                        @endif
                    </div>
                @endif
                @if($invoice->pix_code)
                    <div>
                        <p class="text-xs text-gray-500">PIX</p>
                        <p class="font-mono text-xs break-all">{{ $invoice->pix_code }}</p>
                        @if($invoice->pix_qrcode)
                            <img src="{{ $invoice->pix_qrcode }}" alt="PIX QR Code" class="mt-2 w-32 h-32">
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @if($invoice->status !== 'paid')
            <div class="bg-white rounded-lg shadow-sm p-5">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Registrar Pagamento</h4>
                <form method="POST" action="{{ route('admin.invoices.pay', $invoice) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Método *</label>
                            <select name="method" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="boleto">Boleto</option>
                                <option value="pix">PIX</option>
                                <option value="credit_card">Cartão de Crédito</option>
                                <option value="debit_card">Cartão de Débito</option>
                                <option value="transfer">Transferência</option>
                                <option value="cash">Dinheiro</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Valor *</label>
                            <input type="number" step="0.01" name="amount" value="{{ $invoice->total }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Gateway</label>
                            <input type="text" name="gateway" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="notes" rows="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Confirmar Pagamento</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pagamentos</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Código</th>
                    <th class="pb-2">Método</th>
                    <th class="pb-2">Valor</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Data</th>
                    <th class="pb-2">Gateway</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payments as $payment)
                    <tr class="border-b last:border-0">
                        <td class="py-2 font-mono">{{ $payment->payment_code }}</td>
                        <td class="py-2">{{ $payment->method_label }}</td>
                        <td class="py-2">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                        <td class="py-2">{{ $payment->status_label }}</td>
                        <td class="py-2">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="py-2">{{ $payment->gateway ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="6">Nenhum pagamento registrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <a href="{{ route('admin.invoices.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Faturas</a>
    @if($invoice->status === 'paid')
        <span class="text-xs text-gray-400">Imprimir</span>
    @endif
</div>
@endsection

@push('head')
<style>
@media print {
    body { background: white; }
    .sidebar-transition, .admin-header, .notification-bell { display: none !important; }
}
</style>
@endpush
