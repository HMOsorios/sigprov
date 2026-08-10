@extends('layouts.admin')
@section('title', 'Vinculações')
@section('page-title', 'Vinculações')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.assignments.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Ativo</option>
                <option value="returned" {{ request('status')=='returned' ? 'selected' : '' }}>Devolvido</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select name="client_id" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach(\App\Models\Client::orderBy('name_display')->get() as $c)
                <option value="{{ $c->id }}" {{ request('client_id')==$c->id ? 'selected' : '' }}>{{ $c->name_display }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.assignments.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $assignments->total() }} vinculações</p>
        <a href="{{ route('admin.assignments.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Nova Vinculação</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">Equipamento</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Cliente</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Data Empréstimo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Data Devolução</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Condição</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Responsável</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($assignments as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.equipment.show', $a->equipment) }}" class="text-primary-600 hover:underline font-mono text-xs">{{ $a->equipment->serial }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $a->client->name_display }}</td>
                    <td class="px-4 py-3">{{ $a->assigned_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $a->returned_at ? $a->returned_at->format('d/m/Y') : '-' }}</td>
                    <td class="px-4 py-3">{{ $a->condition_out_label }}</td>
                    <td class="px-4 py-3">{{ $a->assignedBy?->name ?? '-' }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.assignments.show', $a) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detalhes</a>
                        @if($a->isActive())
                        <form method="POST" action="{{ route('admin.assignments.return', $a) }}" onsubmit="return confirm('Registrar devolução deste equipamento?')">
                            @csrf
                            <button type="submit" class="text-amber-600 hover:text-amber-800 text-sm">Devolver</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhuma vinculação encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $assignments->links() }}</div>
</div>
@endsection
