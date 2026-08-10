@extends('layouts.admin')

@section('title', 'Usuários')
@section('page-title', 'Usuários')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Função</label>
            <select name="role_id" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todas</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id')==$role->id ? 'selected' : '' }}>{{ $role->label ?? $role->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $users->total() }} usuários</p>
        <a href="{{ route('admin.users.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Usuário</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nome</th>
                    <th class="p-3 font-medium">Email</th>
                    <th class="p-3 font-medium">Função</th>
                    <th class="p-3 font-medium">Telefone</th>
                    <th class="p-3 font-medium">Ativo</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->email }}</td>
                        <td class="p-3">{{ $user->role?->label ?? $user->role?->name ?? '-' }}</td>
                        <td class="p-3">{{ $user->phone ?? '-' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Sim' : 'Não' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Excluir este usuário?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="6">Nenhum usuário encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $users])
    </div>
</div>
@endsection
