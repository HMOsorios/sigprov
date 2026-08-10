@extends('layouts.admin')
@section('title', 'Nova Vinculação')
@section('page-title', 'Nova Vinculação')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.assignments.store') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Equipamento *</label>
                <select name="equipment_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($equipment as $e)
                    <option value="{{ $e->id }}" {{ old('equipment_id')==$e->id ? 'selected' : '' }}>{{ $e->serial }} - {{ $e->brand }} {{ $e->model }} ({{ $e->type_label }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select name="client_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ old('client_id')==$c->id ? 'selected' : '' }}>{{ $c->name_display }} - {{ $c->document_formatted }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato (opcional)</label>
                <select name="contract_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Contract::orderBy('contract_number')->get() as $ct)
                    <option value="{{ $ct->id }}" {{ old('contract_id')==$ct->id ? 'selected' : '' }}>{{ $ct->contract_number }} - {{ $ct->client->name_display }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condição de Saída</label>
                <select name="condition_out" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="novo" {{ old('condition_out')=='novo' ? 'selected' : '' }}>Novo</option>
                    <option value="bom" {{ old('condition_out')=='bom' ? 'selected' : '' }}>Bom</option>
                    <option value="regular" {{ old('condition_out')=='regular' ? 'selected' : '' }}>Regular</option>
                    <option value="danificado" {{ old('condition_out')=='danificado' ? 'selected' : '' }}>Danificado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.assignments.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
