@extends('layouts.client')

@section('title', 'Minhas Faturas')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Minhas Faturas</h1>
        <p class="text-gray-500 mt-1">Consulte e gerencie suas faturas.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pendente</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalPending }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-2xl">⏳</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Vencido</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $totalOverdue }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-2xl">⚠️</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">Nº da Fatura</th>
                        <th class="px-5 py-3 font-medium">Vencimento</th>
                        <th class="px-5 py-3 font-medium">Valor</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('client.invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $invoice->invoice_number }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $invoice->due_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $invoice->total_formatted }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $colors = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'overdue' => 'bg-red-50 text-red-700 border-red-200', 'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'canceled' => 'bg-gray-50 text-gray-600 border-gray-200', 'refunded' => 'bg-blue-50 text-blue-700 border-blue-200'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colors[$invoice->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $invoice->status_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('client.invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Detalhes</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-500">Nenhuma fatura encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection