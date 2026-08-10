@extends('layouts.admin')
@section('title', 'Elementos de Rede')
@section('page-title', 'Elementos de Rede')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.network-elements.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, identificador, serial..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="olt" {{ request('type')=='olt' ? 'selected' : '' }}>OLT</option>
                <option value="splitter" {{ request('type')=='splitter' ? 'selected' : '' }}>Splitter</option>
                <option value="cto" {{ request('type')=='cto' ? 'selected' : '' }}>CTO</option>
                <option value="drop" {{ request('type')=='drop' ? 'selected' : '' }}>Drop</option>
                <option value="client" {{ request('type')=='client' ? 'selected' : '' }}>Cliente</option>
                <option value="caixa" {{ request('type')=='caixa' ? 'selected' : '' }}>Caixa de Passagem</option>
                <option value="armario" {{ request('type')=='armario' ? 'selected' : '' }}>Armário</option>
                <option value="backbone" {{ request('type')=='backbone' ? 'selected' : '' }}>Backbone</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.network-elements.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $elements->total() }} elementos</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.network-elements.map') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200">Mapa</a>
            <a href="{{ route('admin.network-elements.tree') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200">Árvore</a>
            <a href="{{ route('admin.network-elements.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Elemento</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nome</th>
                    <th class="p-3 font-medium">Tipo</th>
                    <th class="p-3 font-medium">Identificador</th>
                    <th class="p-3 font-medium">Parente</th>
                    <th class="p-3 font-medium">Serial</th>
                    <th class="p-3 font-medium">Modelo</th>
                    <th class="p-3 font-medium">Cidade/UF</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($elements as $element)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $element->name }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                {{ $element->type=='olt' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $element->type=='splitter' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $element->type=='cto' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $element->type=='client' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $element->type=='drop' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $element->type=='caixa' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $element->type=='armario' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $element->type=='backbone' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ !in_array($element->type, ['olt','splitter','cto','client','drop','caixa','armario','backbone']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $element->type_label }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-xs">{{ $element->identifier ?? '-' }}</td>
                        <td class="p-3">{{ $element->parent?->name ?? '-' }}</td>
                        <td class="p-3 font-mono text-xs">{{ $element->serial ?? '-' }}</td>
                        <td class="p-3">{{ $element->model ?? '-' }}</td>
                        <td class="p-3">{{ $element->city ? $element->city . ($element->state ? '/'.$element->state : '') : '-' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $element->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $element->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.network-elements.show', $element) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <a href="{{ route('admin.network-elements.edit', $element) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <form method="POST" action="{{ route('admin.network-elements.destroy', $element) }}" class="inline" onsubmit="return confirm('Excluir este elemento?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="9">Nenhum elemento encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $elements])
    </div>
</div>
@endsection
