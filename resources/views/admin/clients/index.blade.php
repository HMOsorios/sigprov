@extends('layouts.admin')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CPF/CNPJ, email..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inativo</option>
                <option value="blocked" {{ request('status')=='blocked' ? 'selected' : '' }}>Bloqueado</option>
                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Pessoa</label>
            <select name="person_type" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="pf" {{ request('person_type')=='pf' ? 'selected' : '' }}>Física</option>
                <option value="pj" {{ request('person_type')=='pj' ? 'selected' : '' }}>Jurídica</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.clients.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $clients->total() }} clientes</p>
        <a href="{{ route('admin.clients.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Cliente</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Razão Social</th>
                    <th class="p-3 font-medium">Nome Fantasia</th>
                    <th class="p-3 font-medium">CPF/CNPJ</th>
                    <th class="p-3 font-medium">Email</th>
                    <th class="p-3 font-medium">Telefone</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Cidade</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $client->company_name }}</td>
                        <td class="p-3 text-gray-500">{{ $client->fantasy_name ?? '-' }}</td>
                        <td class="p-3 font-mono">{{ $client->cpf_cnpj }}</td>
                        <td class="p-3">{{ $client->email }}</td>
                        <td class="p-3">{{ $client->phone }}</td>
                        <td class="p-3">
                            @php $s = $client->status; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $s=='active' ? 'bg-emerald-100 text-emerald-800' : ($s=='inactive' ? 'bg-gray-100 text-gray-800' : ($s=='blocked' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">
                                {{ $s=='active' ? 'Ativo' : ($s=='inactive' ? 'Inativo' : ($s=='blocked' ? 'Bloqueado' : 'Cancelado')) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $client->city }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.clients.edit', $client) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.clients.show', $client) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Excluir este cliente?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="8">Nenhum cliente encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $clients])
    </div>
</div>
@endsection
