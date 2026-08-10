@extends('layouts.admin')

@section('title', 'Faturas')
@section('page-title', 'Faturas')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Período</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalAmount, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Pendente</p>
        <p class="text-2xl font-bold text-amber-600">R$ {{ number_format($totalPending, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Recebido</p>
        <p class="text-2xl font-bold text-emerald-600">R$ {{ number_format($totalPaid, 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº fatura, cliente..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pendente</option>
                <option value="overdue" {{ request('status')=='overdue' ? 'selected' : '' }}>Vencida</option>
                <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paga</option>
                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Cancelada</option>
                <option value="refunded" {{ request('status')=='refunded' ? 'selected' : '' }}>Estornada</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
            <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
            <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $invoices->total() }} faturas</p>
        <a href="{{ route('admin.invoices.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Nova Fatura</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nº Fatura</th>
                    <th class="p-3 font-medium">Cliente</th>
                    <th class="p-3 font-medium">Emissão</th>
                    <th class="p-3 font-medium">Vencimento</th>
                    <th class="p-3 font-medium">Valor</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-mono">{{ $invoice->invoice_number }}</td>
                        <td class="p-3"><a href="{{ route('admin.clients.show', $invoice->client) }}" class="text-primary-600 hover:underline">{{ $invoice->client->company_name }}</a></td>
                        <td class="p-3">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $invoice->total_formatted }}</td>
                        <td class="p-3">
                            @php $sc = $invoice->status_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Excluir esta fatura?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="7">Nenhuma fatura encontrada</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $invoices])
    </div>
</div>
@endsection
