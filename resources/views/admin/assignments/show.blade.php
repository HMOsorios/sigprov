@extends('layouts.admin')
@section('title', 'Vinculação #'.$assignment->id)
@section('page-title', 'Vinculação #'.$assignment->id)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados da Vinculação</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Equipamento</dt>
                <dd class="font-medium"><a href="{{ route('admin.equipment.show', $assignment->equipment) }}" class="text-primary-600 hover:underline">{{ $assignment->equipment->serial }}</a></dd>
            </div>
            <div>
                <dt class="text-gray-500">Cliente</dt>
                <dd class="font-medium"><a href="{{ route('admin.clients.show', $assignment->client) }}" class="text-primary-600 hover:underline">{{ $assignment->client->name_display }}</a></dd>
            </div>
            <div><dt class="text-gray-500">Contrato</dt><dd class="font-medium">{{ $assignment->contract?->contract_number ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Responsável</dt><dd class="font-medium">{{ $assignment->assignedBy?->name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Data Empréstimo</dt><dd class="font-medium">{{ $assignment->assigned_at->format('d/m/Y H:i') }}</dd></div>
            <div><dt class="text-gray-500">Data Devolução</dt><dd class="font-medium">{{ $assignment->returned_at ? $assignment->returned_at->format('d/m/Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Condição Saída</dt><dd class="font-medium">{{ $assignment->condition_out_label }}</dd></div>
            <div><dt class="text-gray-500">Condição Entrada</dt><dd class="font-medium">{{ $assignment->condition_in_label }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $assignment->notes ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        @if($assignment->isActive())
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Registrar Devolução</h3>
        <form method="POST" action="{{ route('admin.assignments.return', $assignment) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condição Entrada</label>
                    <select name="condition_in" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                        <option value="">Selecione</option>
                        <option value="novo">Novo</option>
                        <option value="bom">Bom</option>
                        <option value="regular">Regular</option>
                        <option value="danificado">Danificado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm"></textarea>
                </div>
                <button type="submit" class="w-full bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700" onclick="return confirm('Confirmar devolução?')">Registrar Devolução</button>
            </div>
        </form>
        @else
        <p class="text-sm text-gray-500">Equipamento já devolvido.</p>
        @endif
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.assignments.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Vinculações</a>
</div>
@endsection
