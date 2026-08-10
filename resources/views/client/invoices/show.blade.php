@extends('layouts.client')

@section('title', "Fatura {$invoice->invoice_number}")

@section('content')
    <div class="mb-6">
        <a href="{{ route('client.invoices.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">&larr; Voltar para Faturas</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Fatura {{ $invoice->invoice_number }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalhes da Fatura</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Número</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @php $colors = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'overdue' => 'bg-red-50 text-red-700 border-red-200', 'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'canceled' => 'bg-gray-50 text-gray-600 border-gray-200', 'refunded' => 'bg-blue-50 text-blue-700 border-blue-200'] @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colors[$invoice->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $invoice->status_label }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data de Emissão</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data de Vencimento</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</p>
                    </div>
                    @if($invoice->paid_date)
                        <div>
                            <p class="text-sm text-gray-500">Data de Pagamento</p>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->paid_date->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Valores</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Valor Original</span>
                            <span class="text-gray-900">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</span>
                        </div>
                        @if($invoice->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Desconto</span>
                                <span class="text-emerald-600">- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($invoice->late_fee > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Multa</span>
                                <span class="text-red-600">+ R$ {{ number_format($invoice->late_fee, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($invoice->interest > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Juros</span>
                                <span class="text-red-600">+ R$ {{ number_format($invoice->interest, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm font-bold border-t border-gray-200 pt-2">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">{{ $invoice->total_formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($invoice->payments->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Pagamentos</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-100">
                                    <th class="pb-2 font-medium">Código</th>
                                    <th class="pb-2 font-medium">Método</th>
                                    <th class="pb-2 font-medium">Valor</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr class="border-b border-gray-50">
                                        <td class="py-2.5 text-gray-600">{{ $payment->payment_code }}</td>
                                        <td class="py-2.5 text-gray-900">{{ $payment->method_label }}</td>
                                        <td class="py-2.5 font-medium text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                        <td class="py-2.5">
                                            @php $pcolors = ['pending' => 'bg-amber-50 text-amber-700', 'confirmed' => 'bg-emerald-50 text-emerald-700', 'failed' => 'bg-red-50 text-red-700', 'refunded' => 'bg-blue-50 text-blue-700'] @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pcolors[$payment->status] ?? 'bg-gray-50 text-gray-600' }}">{{ $payment->status_label }}</span>
                                        </td>
                                        <td class="py-2.5 text-gray-500">{{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if(in_array($invoice->status, ['pending', 'overdue']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pagamento</h2>
                    @if($invoice->boleto_url)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Boleto Bancário</p>
                            <a href="{{ $invoice->boleto_url }}" target="_blank" class="inline-flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                                <span>📄</span> Visualizar Boleto
                            </a>
                            @if($invoice->boleto_barcode)
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 mb-1">Linha Digitável</p>
                                    <p class="text-xs font-mono text-gray-700 bg-gray-50 p-2 rounded border border-gray-200 break-all">{{ $invoice->boleto_barcode }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    @if($invoice->pix_code)
                        <div>
                            <p class="text-sm text-gray-500 mb-2">PIX</p>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">Código PIX</p>
                                <p class="text-xs font-mono text-gray-700 break-all">{{ $invoice->pix_code }}</p>
                            </div>
                            @if($invoice->pix_qrcode)
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 mb-1">QR Code</p>
                                    <img src="{{ $invoice->pix_qrcode }}" alt="QR Code PIX" class="w-32 h-32 border border-gray-200 rounded-lg">
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if($invoice->contract)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contrato</h2>
                    <p class="text-sm text-gray-500">Número</p>
                    <p class="text-sm font-medium text-gray-900">{{ $invoice->contract->contract_number }}</p>
                    @if($invoice->contract->plan)
                        <p class="text-sm text-gray-500 mt-2">Plano</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->contract->plan->name ?? 'N/A' }}</p>
                    @endif
                </div>
            @endif

            @if($invoice->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Observações</h2>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection