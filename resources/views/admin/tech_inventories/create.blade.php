@extends('layouts.admin')
@section('title', 'Nova Retirada')
@section('page-title', 'Registrar Retirada de Equipamento')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.tech-inventories.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Técnico *</label>
                <select name="technician_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($technicians as $t)
                    <option value="{{ $t->id }}" {{ old('technician_id')==$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Equipamento *</label>
                <select name="equipment_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($equipment as $eq)
                    <option value="{{ $eq->id }}" {{ old('equipment_id')==$eq->id ? 'selected' : '' }}>{{ $eq->serial }} - {{ $eq->model }} ({{ $eq->type_label }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">OS #</label>
                <select name="work_order_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Nenhuma</option>
                    @foreach(\App\Models\WorkOrder::orderBy('id')->get() as $wo)
                    <option value="{{ $wo->id }}" {{ old('work_order_id')==$wo->id ? 'selected' : '' }}>#{{ $wo->id }} - {{ $wo->client?->name_display ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condição de Saída *</label>
                <select name="condition_out" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="novo" {{ old('condition_out')=='novo' ? 'selected' : '' }}>Novo</option>
                    <option value="bom" {{ old('condition_out')=='bom' ? 'selected' : '' }}>Bom</option>
                    <option value="regular" {{ old('condition_out')=='regular' ? 'selected' : '' }}>Regular</option>
                    <option value="danificado" {{ old('condition_out')=='danificado' ? 'selected' : '' }}>Danificado</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.tech-inventories.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
