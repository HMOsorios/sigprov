@extends('layouts.admin')
@section('title', 'Novo Equipamento')
@section('page-title', 'Novo Equipamento')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.equipment.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial *</label>
                <input type="text" name="serial" value="{{ old('serial') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patrimônio</label>
                <input type="text" name="patrimony" value="{{ old('patrimony') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="router">Roteador</option>
                    <option value="onu">ONU</option>
                    <option value="ont">ONT</option>
                    <option value="modem">Modem</option>
                    <option value="cto">CTO</option>
                    <option value="splitter">Splitter</option>
                    <option value="ups">UPS</option>
                    <option value="antena">Antena</option>
                    <option value="fonte">Fonte</option>
                    <option value="cabo">Cabo</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                <input type="text" name="model" value="{{ old('model') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">MAC</label>
                <input type="text" name="mac" value="{{ old('mac') }}" placeholder="00:00:00:00:00:00" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP</label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Firmware</label>
                <input type="text" name="firmware_version" value="{{ old('firmware_version') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Compra</label>
                <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data Compra</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
                <select name="supplier_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Supplier::orderBy('legal_name')->get() as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_id')==$s->id ? 'selected' : '' }}>{{ $s->legal_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="disponivel">Disponível</option>
                    <option value="emprestado">Emprestado</option>
                    <option value="manutencao">Manutenção</option>
                    <option value="descartado">Descartado</option>
                    <option value="perdido">Perdido</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.equipment.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
