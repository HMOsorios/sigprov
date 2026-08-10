@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Olá, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-500 mt-1">Bem-vindo à sua área do cliente.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Contratos Ativos</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeContracts }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center text-2xl">📄</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Faturas Pendentes</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingInvoices }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-2xl">⏳</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Faturas Vencidas</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $overdueInvoices }}</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-2xl">⚠️</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Chamados Abertos</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $openTickets }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-2xl">🎫</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Faturas Recentes</h2>
                <a href="{{ route('client.invoices.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Ver todas</a>
            </div>
            <div class="p-5">
                @if($recentInvoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-100">
                                    <th class="pb-2 font-medium">Nº</th>
                                    <th class="pb-2 font-medium">Vencimento</th>
                                    <th class="pb-2 font-medium">Valor</th>
                                    <th class="pb-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInvoices as $invoice)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="py-2.5">
                                            <a href="{{ route('client.invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $invoice->invoice_number }}</a>
                                        </td>
                                        <td class="py-2.5 text-gray-600">{{ $invoice->due_date->format('d/m/Y') }}</td>
                                        <td class="py-2.5 font-medium text-gray-900">{{ $invoice->total_formatted }}</td>
                                        <td class="py-2.5">@php $colors = ['pending' => 'bg-amber-50 text-amber-700 border-amber-200', 'overdue' => 'bg-red-50 text-red-700 border-red-200', 'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'canceled' => 'bg-gray-50 text-gray-600 border-gray-200', 'refunded' => 'bg-blue-50 text-blue-700 border-blue-200'] @endphp<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colors[$invoice->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $invoice->status_label }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Nenhuma fatura encontrada.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Chamados Recentes</h2>
                <a href="{{ route('client.tickets.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Ver todos</a>
            </div>
            <div class="p-5">
                @if($recentTickets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-100">
                                    <th class="pb-2 font-medium">Nº</th>
                                    <th class="pb-2 font-medium">Assunto</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="py-2.5">
                                            <a href="{{ route('client.tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $ticket->ticket_number }}</a>
                                        </td>
                                        <td class="py-2.5 text-gray-600 max-w-[200px] truncate">{{ $ticket->subject }}</td>
                                        <td class="py-2.5">@php $tcolors = ['open' => 'bg-red-50 text-red-700 border-red-200', 'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200', 'waiting_client' => 'bg-blue-50 text-blue-700 border-blue-200', 'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'closed' => 'bg-gray-50 text-gray-600 border-gray-200'] @endphp<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $tcolors[$ticket->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->status_label }}</span></td>
                                        <td class="py-2.5 text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Nenhum chamado encontrado.</p>
                @endif
            </div>
        </div>
    </div>
@endsection