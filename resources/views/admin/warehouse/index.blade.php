@extends('layouts.admin')
@section('title', 'Almoxarifado')
@section('page-title', 'Almoxarifado')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-primary-500">
        <p class="text-sm text-gray-500">Total de Itens</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_items'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Valor Total</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Estoque Baixo</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['low_stock'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Sem Estoque</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['out_of_stock'] }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.warehouse.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU, nome..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="category" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estoque</label>
            <select name="stock" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="low" {{ request('stock')=='low' ? 'selected' : '' }}>Estoque Baixo</option>
                <option value="out" {{ request('stock')=='out' ? 'selected' : '' }}>Sem Estoque</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.warehouse.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $items->total() }} itens</p>
        <a href="{{ route('admin.warehouse.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Item</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">SKU</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Nome</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Categoria</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Qtd Atual</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Est. Mínimo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Un.</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Preço Un.</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Valor Total</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($items as $item)
                @php $sc = $item->stock_status_color; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">{{ $item->sku }}</td>
                    <td class="px-4 py-3">{{ $item->name }}</td>
                    <td class="px-4 py-3">{{ $item->category ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $item->current_qty }}</td>
                    <td class="px-4 py-3">{{ $item->min_stock }}</td>
                    <td class="px-4 py-3">{{ $item->unit }}</td>
                    <td class="px-4 py-3">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">R$ {{ number_format($item->total_value, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                            {{ $item->stock_status }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.warehouse.show', $item) }}" class="text-primary-600 hover:text-primary-800 text-sm">Detalhes</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-4 py-8 text-center text-gray-500">Nenhum item encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
