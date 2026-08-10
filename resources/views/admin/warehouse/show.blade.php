@extends('layouts.admin')
@section('title', $warehouseItem->name)
@section('page-title', $warehouseItem->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Item</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">SKU</dt><dd class="font-medium font-mono">{{ $warehouseItem->sku }}</dd></div>
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $warehouseItem->name }}</dd></div>
            <div><dt class="text-gray-500">Categoria</dt><dd class="font-medium">{{ $warehouseItem->category ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Unidade</dt><dd class="font-medium">{{ $warehouseItem->unit }}</dd></div>
            <div><dt class="text-gray-500">Preço Unitário</dt><dd class="font-medium">R$ {{ number_format($warehouseItem->unit_price, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Preço de Custo</dt><dd class="font-medium">{{ $warehouseItem->cost_price ? 'R$ '.number_format($warehouseItem->cost_price, 2, ',', '.') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Localização</dt><dd class="font-medium">{{ $warehouseItem->location ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Fornecedor</dt><dd class="font-medium">{{ $warehouseItem->supplier?->legal_name ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $warehouseItem->notes ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Estoque</h3>
        @php $sc = $warehouseItem->stock_status_color; @endphp
        <div class="text-center mb-4">
            <p class="text-4xl font-bold text-gray-800">{{ $warehouseItem->current_qty }}</p>
            <p class="text-sm text-gray-500">{{ $warehouseItem->unit }}</p>
            <span class="inline-block mt-2 px-3 py-1 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                {{ $warehouseItem->stock_status }}
            </span>
        </div>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><span class="text-gray-500">Estoque Mínimo</span><span class="font-medium">{{ $warehouseItem->min_stock }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Estoque Máximo</span><span class="font-medium">{{ $warehouseItem->max_stock ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Valor Total</span><span class="font-medium">R$ {{ number_format($warehouseItem->total_value, 2, ',', '.') }}</span></div>
        </dl>
        <hr class="my-4">
        <a href="{{ route('admin.warehouse.edit', $warehouseItem) }}" class="w-full text-center block bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Registrar Movimentação</h3>
        <form method="POST" action="{{ route('admin.warehouse.movement', $warehouseItem) }}">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                        <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                            <option value="in">Entrada</option>
                            <option value="out">Saída</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                        <input type="number" min="1" name="qty" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Referência</label>
                    <select name="reference_type" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                        <option value="">Selecione</option>
                        <option value="purchase">Compra</option>
                        <option value="sale">Venda</option>
                        <option value="transfer">Transferência</option>
                        <option value="adjustment">Ajuste</option>
                        <option value="return">Devolução</option>
                        <option value="install">Instalação</option>
                        <option value="maintenance">Manutenção</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea name="notes" rows="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm"></textarea>
                </div>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Registrar</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Histórico de Movimentações</h3>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Data</th><th class="pb-2">Tipo</th><th class="pb-2">Qtd</th><th class="pb-2">Referência</th><th class="pb-2">Resp.</th></tr></thead>
                <tbody>
                    @forelse($warehouseItem->movements as $m)
                    @php $tc = $m->type_color; @endphp
                    <tr class="border-b last:border-0">
                        <td class="py-2 text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $tc=='success' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $m->type_label }}
                            </span>
                        </td>
                        <td class="py-2 font-semibold">{{ $m->qty }}</td>
                        <td class="py-2">{{ $m->reference_type_label }}</td>
                        <td class="py-2">{{ $m->responsible?->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="5">Nenhuma movimentação.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.warehouse.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Almoxarifado</a>
</div>
@endsection
