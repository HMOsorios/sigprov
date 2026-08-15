@extends('layouts.admin')
@section('title', 'Leads')
@section('page-title', 'Leads')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.leads.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email, telefone..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="new" {{ request('status')=='new' ? 'selected' : '' }}>Novo</option>
                <option value="contacted" {{ request('status')=='contacted' ? 'selected' : '' }}>Contactado</option>
                <option value="proposal" {{ request('status')=='proposal' ? 'selected' : '' }}>Proposta</option>
                <option value="negotiation" {{ request('status')=='negotiation' ? 'selected' : '' }}>Negociação</option>
                <option value="won" {{ request('status')=='won' ? 'selected' : '' }}>Ganho</option>
                <option value="lost" {{ request('status')=='lost' ? 'selected' : '' }}>Perdido</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Origem</label>
            <select name="source" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todas</option>
                @foreach($sources as $src)
                <option value="{{ $src }}" {{ request('source')==$src ? 'selected' : '' }}>{{ $src }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
            <select name="assigned_to" class="rounded border-gray-300 border px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($staff as $user)
                <option value="{{ $user->id }}" {{ request('assigned_to')==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.leads.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600">Total: {{ $leads->total() }} leads</p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.kanban') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200">Kanban</a>
            <a href="{{ route('admin.leads.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Lead</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.leads.bulk-update-status') }}" id="bulkForm">
        @csrf
        <div class="px-5 py-3 border-b bg-gray-50 flex items-center gap-3">
            <span class="text-sm text-gray-600">Ações em massa:</span>
            <select name="status" class="rounded border-gray-300 border px-3 py-1.5 text-sm">
                <option value="new">Novo</option>
                <option value="contacted">Contactado</option>
                <option value="proposal">Proposta</option>
                <option value="negotiation">Negociação</option>
                <option value="won">Ganho</option>
                <option value="lost">Perdido</option>
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-1.5 rounded text-sm hover:bg-primary-700" onclick="return confirm('Atualizar status dos leads selecionados?')">Atualizar Status</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 w-10"><input type="checkbox" id="selectAll" class="rounded border-gray-300"></th>
                        <th class="px-4 py-3 font-medium text-gray-600">Nome</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Contato</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Plano de Interesse</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Origem</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Responsável</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-600">Data</th>
                        <th class="px-4 py-3 font-medium text-gray-600"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="rounded border-gray-300 lead-checkbox"></td>
                        <td class="px-4 py-3 font-medium">{{ $lead->name }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs">{{ $lead->email }}</div>
                            <div class="text-xs text-gray-500">{{ $lead->phone ?? $lead->cellphone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $lead->interest_plan ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800">{{ $lead->source ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $lead->assignedTo?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $sc = match($lead->status) {
                                    'new' => 'bg-blue-100 text-blue-800',
                                    'contacted' => 'bg-amber-100 text-amber-800',
                                    'proposal' => 'bg-indigo-100 text-indigo-800',
                                    'negotiation' => 'bg-purple-100 text-purple-800',
                                    'won' => 'bg-emerald-100 text-emerald-800',
                                    'lost' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $sl = match($lead->status) {
                                    'new' => 'Novo',
                                    'contacted' => 'Contactado',
                                    'proposal' => 'Proposta',
                                    'negotiation' => 'Negociação',
                                    'won' => 'Ganho',
                                    'lost' => 'Perdido',
                                    default => $lead->status
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc }}">{{ $sl }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs">{{ $lead->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.leads.show', $lead) }}" class="text-primary-600 hover:text-primary-800 text-sm">Ver</a>
                            <a href="{{ route('admin.leads.edit', $lead) }}" class="text-amber-600 hover:text-amber-800 text-sm">Editar</a>
                            @if($lead->status !== 'won')
                            <a href="{{ route('admin.leads.convert', $lead) }}" class="text-emerald-600 hover:text-emerald-800 text-sm">Converter</a>
                            @endif
                            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="inline" onsubmit="return confirm('Excluir este lead?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Nenhum lead encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="p-4">{{ $leads->links() }}</div>
</div>

@push('scripts')
<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.lead-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection
