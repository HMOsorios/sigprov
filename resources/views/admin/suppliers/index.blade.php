@extends('layouts.admin')
@section('title', 'Fornecedores')
@section('page-title', 'Fornecedores')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CNPJ, contato..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.suppliers.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $suppliers->total() }} fornecedores</p>
        <a href="{{ route('admin.suppliers.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Fornecedor</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">CNPJ</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Razão Social</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Nome Fantasia</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Contato</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Email</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Telefone</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Categoria</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($suppliers as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">{{ $s->cnpj }}</td>
                    <td class="px-4 py-3">{{ $s->legal_name }}</td>
                    <td class="px-4 py-3">{{ $s->trade_name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $s->contact ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $s->email ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $s->phone ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $s->category_label }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $s->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $s->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.suppliers.show', $s) }}" class="text-primary-600 hover:text-primary-800 text-sm">Detalhes</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Nenhum fornecedor encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $suppliers->links() }}</div>
</div>
@endsection
