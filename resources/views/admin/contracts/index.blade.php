@extends('layouts.admin')

@section('title', 'Contratos')
@section('page-title', 'Contratos')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.contracts.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº contrato, cliente..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="suspended" {{ request('status')=='suspended' ? 'selected' : '' }}>Suspenso</option>
                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Cancelado</option>
                <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>Expirado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Plano</label>
            <select name="plan_id" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request('plan_id')==$plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.contracts.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $contracts->total() }} contratos</p>
        <a href="{{ route('admin.contracts.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Contrato</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nº Contrato</th>
                    <th class="p-3 font-medium">Cliente</th>
                    <th class="p-3 font-medium">Plano</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Início</th>
                    <th class="p-3 font-medium">Vencimento</th>
                    <th class="p-3 font-medium">Valor</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-mono">{{ $contract->contract_number }}</td>
                        <td class="p-3"><a href="{{ route('admin.clients.show', $contract->client) }}" class="text-primary-600 hover:underline">{{ $contract->client->company_name }}</a></td>
                        <td class="p-3">{{ $contract->plan->name ?? '-' }}</td>
                        <td class="p-3">
                            @php $sc = $contract->status_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $contract->status_label }}
                            </span>
                        </td>
                        <td class="p-3">{{ $contract->start_date->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $contract->due_day }}</td>
                        <td class="p-3">R$ {{ number_format($contract->signed_price, 2, ',', '.') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.contracts.edit', $contract) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.contracts.destroy', $contract) }}" class="inline" onsubmit="return confirm('Excluir este contrato?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="8">Nenhum contrato encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $contracts])
    </div>
</div>
@endsection
