@extends('layouts.admin')

@section('title', 'Chamados')
@section('page-title', 'Chamados')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Abertos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $counters['open'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Resolvidos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $counters['resolved'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-gray-500">
        <p class="text-sm text-gray-500">Fechados</p>
        <p class="text-2xl font-bold text-gray-800">{{ $counters['closed'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Críticos</p>
        <p class="text-2xl font-bold text-amber-600">{{ $counters['critical'] }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nº, assunto, cliente..." class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                <option value="open" {{ request('status')=='open' ? 'selected' : '' }}>Aberto</option>
                <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>Em Andamento</option>
                <option value="waiting_client" {{ request('status')=='waiting_client' ? 'selected' : '' }}>Aguardando Cliente</option>
                <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolvido</option>
                <option value="closed" {{ request('status')=='closed' ? 'selected' : '' }}>Fechado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
            <select name="priority" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todas</option>
                <option value="low" {{ request('priority')=='low' ? 'selected' : '' }}>Baixa</option>
                <option value="medium" {{ request('priority')=='medium' ? 'selected' : '' }}>Média</option>
                <option value="high" {{ request('priority')=='high' ? 'selected' : '' }}>Alta</option>
                <option value="critical" {{ request('priority')=='critical' ? 'selected' : '' }}>Crítica</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Técnico</label>
            <select name="assigned_to" class="rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todos</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ request('assigned_to')==$tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Filtrar</button>
        <a href="{{ route('admin.tickets.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Limpar</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $tickets->total() }} chamados</p>
        <a href="{{ route('admin.tickets.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Chamado</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nº</th>
                    <th class="p-3 font-medium">Cliente</th>
                    <th class="p-3 font-medium">Assunto</th>
                    <th class="p-3 font-medium">Prioridade</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Técnico</th>
                    <th class="p-3 font-medium">Criado em</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-mono">{{ $ticket->ticket_number }}</td>
                        <td class="p-3"><a href="{{ route('admin.clients.show', $ticket->client) }}" class="text-primary-600 hover:underline">{{ $ticket->client->company_name }}</a></td>
                        <td class="p-3 max-w-[200px] truncate">{{ $ticket->subject }}</td>
                        <td class="p-3">
                            @php $pc = $ticket->priority_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $pc=='danger' ? 'bg-red-100 text-red-800' : ($pc=='warning' ? 'bg-amber-100 text-amber-800' : ($pc=='blue' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $ticket->priority_label }}
                            </span>
                        </td>
                        <td class="p-3">
                            @php $sc = $ticket->status_color; @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : ($sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td class="p-3">{{ $ticket->assignedTo->name ?? '-' }}</td>
                        <td class="p-3">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 mr-2">Ver</a>
                            <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" class="inline" onsubmit="return confirm('Excluir este chamado?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="8">Nenhum chamado encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $tickets])
    </div>
</div>
@endsection
