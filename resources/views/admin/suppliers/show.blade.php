@extends('layouts.admin')
@section('title', $supplier->legal_name)
@section('page-title', $supplier->legal_name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Fornecedor</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">CNPJ</dt><dd class="font-medium font-mono">{{ $supplier->cnpj }}</dd></div>
            <div><dt class="text-gray-500">Razão Social</dt><dd class="font-medium">{{ $supplier->legal_name }}</dd></div>
            <div><dt class="text-gray-500">Nome Fantasia</dt><dd class="font-medium">{{ $supplier->trade_name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Contato</dt><dd class="font-medium">{{ $supplier->contact ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $supplier->email ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Telefone</dt><dd class="font-medium">{{ $supplier->phone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Celular</dt><dd class="font-medium">{{ $supplier->cellphone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Categoria</dt><dd class="font-medium">{{ $supplier->category_label }}</dd></div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd><span class="px-2 py-0.5 text-xs rounded-full {{ $supplier->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">{{ $supplier->status_label }}</span></dd>
            </div>
            <div><dt class="text-gray-500">CEP</dt><dd class="font-medium">{{ $supplier->zip_code ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Endereço</dt><dd class="font-medium">{{ $supplier->address ?? '-' }}, {{ $supplier->number ?? '-' }}{{ $supplier->complement ? ' - '.$supplier->complement : '' }} - {{ $supplier->neighborhood ?? '-' }}, {{ $supplier->city ?? '-' }}-{{ $supplier->state ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $supplier->notes ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações</h3>
        <div class="flex flex-col gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Equipamentos</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Serial</th><th class="pb-2">Tipo</th><th class="pb-2">Marca/Modelo</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($supplier->equipment as $eq)
                    <tr class="border-b last:border-0">
                        <td class="py-2"><a href="{{ route('admin.equipment.show', $eq) }}" class="text-primary-600 hover:underline font-mono text-xs">{{ $eq->serial }}</a></td>
                        <td class="py-2">{{ $eq->type_label }}</td>
                        <td class="py-2">{{ $eq->brand }} {{ $eq->model }}</td>
                        <td class="py-2">{{ $eq->status_label }}</td>
                    </tr>
                    @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="4">Nenhum equipamento.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Itens em Estoque</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">SKU</th><th class="pb-2">Nome</th><th class="pb-2">Qtd</th><th class="pb-2">Preço</th></tr></thead>
                <tbody>
                    @forelse($supplier->warehouseItems as $wi)
                    <tr class="border-b last:border-0">
                        <td class="py-2"><a href="{{ route('admin.warehouse.show', $wi) }}" class="text-primary-600 hover:underline font-mono text-xs">{{ $wi->sku }}</a></td>
                        <td class="py-2">{{ $wi->name }}</td>
                        <td class="py-2">{{ $wi->current_qty }}</td>
                        <td class="py-2">R$ {{ number_format($wi->unit_price, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="4">Nenhum item em estoque.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.suppliers.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Fornecedores</a>
</div>
@endsection
