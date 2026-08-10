@extends('layouts.admin')
@section('title', 'Editar Equipamento')
@section('page-title', 'Editar Equipamento')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.equipment.update', $equipment) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial *</label>
                <input type="text" name="serial" value="{{ old('serial', $equipment->serial) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patrimônio</label>
                <input type="text" name="patrimony" value="{{ old('patrimony', $equipment->patrimony) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="router" {{ old('type', $equipment->type)=='router' ? 'selected' : '' }}>Roteador</option>
                    <option value="onu" {{ old('type', $equipment->type)=='onu' ? 'selected' : '' }}>ONU</option>
                    <option value="ont" {{ old('type', $equipment->type)=='ont' ? 'selected' : '' }}>ONT</option>
                    <option value="modem" {{ old('type', $equipment->type)=='modem' ? 'selected' : '' }}>Modem</option>
                    <option value="cto" {{ old('type', $equipment->type)=='cto' ? 'selected' : '' }}>CTO</option>
                    <option value="splitter" {{ old('type', $equipment->type)=='splitter' ? 'selected' : '' }}>Splitter</option>
                    <option value="ups" {{ old('type', $equipment->type)=='ups' ? 'selected' : '' }}>UPS</option>
                    <option value="antena" {{ old('type', $equipment->type)=='antena' ? 'selected' : '' }}>Antena</option>
                    <option value="fonte" {{ old('type', $equipment->type)=='fonte' ? 'selected' : '' }}>Fonte</option>
                    <option value="cabo" {{ old('type', $equipment->type)=='cabo' ? 'selected' : '' }}>Cabo</option>
                    <option value="outro" {{ old('type', $equipment->type)=='outro' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" name="brand" value="{{ old('brand', $equipment->brand) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                <input type="text" name="model" value="{{ old('model', $equipment->model) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">MAC</label>
                <input type="text" name="mac" value="{{ old('mac', $equipment->mac) }}" placeholder="00:00:00:00:00:00" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP</label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $equipment->ip_address) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Firmware</label>
                <input type="text" name="firmware_version" value="{{ old('firmware_version', $equipment->firmware_version) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Compra</label>
                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $equipment->purchase_price) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Compra</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
                <select name="supplier_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Supplier::orderBy('legal_name')->get() as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_id', $equipment->supplier_id)==$s->id ? 'selected' : '' }}>{{ $s->legal_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="disponivel" {{ old('status', $equipment->status)=='disponivel' ? 'selected' : '' }}>Disponível</option>
                    <option value="emprestado" {{ old('status', $equipment->status)=='emprestado' ? 'selected' : '' }}>Emprestado</option>
                    <option value="manutencao" {{ old('status', $equipment->status)=='manutencao' ? 'selected' : '' }}>Manutenção</option>
                    <option value="descartado" {{ old('status', $equipment->status)=='descartado' ? 'selected' : '' }}>Descartado</option>
                    <option value="perdido" {{ old('status', $equipment->status)=='perdido' ? 'selected' : '' }}>Perdido</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes', $equipment->notes) }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Atualizar</button>
            <a href="{{ route('admin.equipment.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
