@extends('layouts.admin')
@section('title', 'Nova OS')
@section('page-title', 'Nova Ordem de Serviço')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.work-orders.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select name="client_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach(\App\Models\Client::orderBy('name_display')->get() as $c)
                    <option value="{{ $c->id }}" {{ old('client_id')==$c->id ? 'selected' : '' }}>{{ $c->name_display }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato</label>
                <select name="contract_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Contract::orderBy('id')->get() as $ct)
                    <option value="{{ $ct->id }}" {{ old('contract_id')==$ct->id ? 'selected' : '' }}>{{ $ct->id }} - {{ $ct->client?->name_display ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="install" {{ old('type')=='install' ? 'selected' : '' }}>Instalação</option>
                    <option value="maintenance" {{ old('type')=='maintenance' ? 'selected' : '' }}>Manutenção</option>
                    <option value="repair" {{ old('type')=='repair' ? 'selected' : '' }}>Reparo</option>
                    <option value="remove" {{ old('type')=='remove' ? 'selected' : '' }}>Retirada</option>
                    <option value="visit" {{ old('type')=='visit' ? 'selected' : '' }}>Visita</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade *</label>
                <select name="priority" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="medium" {{ old('priority')=='medium' ? 'selected' : '' }}>Média</option>
                    <option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Baixa</option>
                    <option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>Alta</option>
                    <option value="critical" {{ old('priority')=='critical' ? 'selected' : '' }}>Crítica</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agendado para</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Técnico</label>
                <select name="technician_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($technicians as $t)
                    <option value="{{ $t->id }}" {{ old('technician_id')==$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                <textarea name="description" rows="3" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.work-orders.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
