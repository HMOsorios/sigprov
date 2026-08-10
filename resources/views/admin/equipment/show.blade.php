@extends('layouts.admin')
@section('title', 'Equipamento: '.$equipment->serial)
@section('page-title', 'Equipamento: '.$equipment->serial)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Equipamento</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Serial</dt><dd class="font-medium font-mono">{{ $equipment->serial }}</dd></div>
            <div><dt class="text-gray-500">Patrimônio</dt><dd class="font-medium">{{ $equipment->patrimony ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium">{{ $equipment->type_label }}</dd></div>
            <div><dt class="text-gray-500">Marca</dt><dd class="font-medium">{{ $equipment->brand ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Modelo</dt><dd class="font-medium">{{ $equipment->model ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">MAC</dt><dd class="font-medium font-mono">{{ $equipment->mac ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">IP</dt><dd class="font-medium font-mono">{{ $equipment->ip_address ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Firmware</dt><dd class="font-medium">{{ $equipment->firmware_version ?? '-' }}</dd></div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd>
                    @php $sc = $equipment->status_color; @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                        {{ $equipment->status_label }}
                    </span>
                </dd>
            </div>
            <div><dt class="text-gray-500">Preço Compra</dt><dd class="font-medium">{{ $equipment->purchase_price ? 'R$ '.number_format($equipment->purchase_price, 2, ',', '.') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Data Compra</dt><dd class="font-medium">{{ $equipment->purchase_date ? $equipment->purchase_date->format('d/m/Y') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Fornecedor</dt><dd class="font-medium">{{ $equipment->supplier?->legal_name ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $equipment->notes ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cliente Atual</h3>
        @php $current = $equipment->currentAssignment; @endphp
        @if($current)
            <p class="font-medium text-sm">{{ $current->client->name_display }}</p>
            <p class="text-sm text-gray-500">Desde {{ $current->assigned_at->format('d/m/Y') }}</p>
            <a href="{{ route('admin.assignments.show', $current) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Vinculação</a>
        @else
            <p class="text-sm text-gray-500">Nenhum cliente vinculado.</p>
        @endif
        <hr class="my-4">
        <div class="flex flex-col gap-2">
            <a href="{{ route('admin.equipment.edit', $equipment) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Histórico de Vinculações</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Cliente</th><th class="pb-2">Data Empréstimo</th><th class="pb-2">Data Devolução</th><th class="pb-2">Condição Saída</th><th class="pb-2">Condição Entrada</th><th class="pb-2">Responsável</th></tr></thead>
            <tbody>
                @forelse($equipment->assignments as $a)
                    <tr class="border-b last:border-0">
                        <td class="py-2"><a href="{{ route('admin.clients.show', $a->client) }}" class="text-primary-600 hover:underline">{{ $a->client->name_display }}</a></td>
                        <td class="py-2">{{ $a->assigned_at->format('d/m/Y') }}</td>
                        <td class="py-2">{{ $a->returned_at ? $a->returned_at->format('d/m/Y') : '-' }}</td>
                        <td class="py-2">{{ $a->condition_out_label }}</td>
                        <td class="py-2">{{ $a->condition_in_label }}</td>
                        <td class="py-2">{{ $a->assignedBy?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="6">Nenhuma vinculação registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.equipment.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Equipamentos</a>
</div>
@endsection
