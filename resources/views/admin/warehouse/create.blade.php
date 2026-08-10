@extends('layouts.admin')
@section('title', 'Novo Item')
@section('page-title', 'Novo Item')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.warehouse.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label>
                <input type="text" name="sku" value="{{ old('sku') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <input type="text" name="category" value="{{ old('category') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidade *</label>
                <select name="unit" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    <option value="un" {{ old('unit')=='un' ? 'selected' : '' }}>Unidade</option>
                    <option value="metro" {{ old('unit')=='metro' ? 'selected' : '' }}>Metro</option>
                    <option value="caixa" {{ old('unit')=='caixa' ? 'selected' : '' }}>Caixa</option>
                    <option value="pct" {{ old('unit')=='pct' ? 'selected' : '' }}>Pacote</option>
                    <option value="kg" {{ old('unit')=='kg' ? 'selected' : '' }}>Quilograma</option>
                    <option value="litro" {{ old('unit')=='litro' ? 'selected' : '' }}>Litro</option>
                    <option value="par" {{ old('unit')=='par' ? 'selected' : '' }}>Par</option>
                    <option value="jogo" {{ old('unit')=='jogo' ? 'selected' : '' }}>Jogo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unitário *</label>
                <input type="number" step="0.01" min="0" name="unit_price" value="{{ old('unit_price') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço de Custo</label>
                <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Mínimo *</label>
                <input type="number" min="0" name="min_stock" value="{{ old('min_stock', 0) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estoque Máximo</label>
                <input type="number" min="0" name="max_stock" value="{{ old('max_stock') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Atual *</label>
                <input type="number" min="0" name="current_qty" value="{{ old('current_qty', 0) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Ex: Prateleira A1" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
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
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.warehouse.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
