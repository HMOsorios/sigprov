@extends('layouts.admin')

@section('title', 'Chamado '.$ticket->ticket_number)
@section('page-title', $ticket->subject)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Mensagens</h3>
            <div class="space-y-4 max-h-[600px] overflow-y-auto">
                @foreach($ticket->messages as $msg)
                    <div class="flex gap-3 {{ $msg->is_system ? 'opacity-60' : '' }} {{ $msg->is_internal ? 'bg-amber-50 rounded-lg p-3' : '' }}">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ substr($msg->user?->name ?? 'Sistema', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-sm">{{ $msg->user?->name ?? 'Sistema' }}</span>
                                <span class="text-xs text-gray-400">{{ $msg->created_at ? $msg->created_at->format('d/m/Y H:i') : '' }}</span>
                                @if($msg->is_system)
                                    <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">Sistema</span>
                                @endif
                                @if($msg->is_internal)
                                    <span class="px-1.5 py-0.5 text-xs bg-amber-100 text-amber-700 rounded">Interno</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $msg->message }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Responder</h3>
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                @csrf
                <div class="space-y-3">
                    <textarea name="message" rows="4" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Digite sua resposta..." required></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_internal" value="1">
                            Nota interna (não visível ao cliente)
                        </label>
                        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Enviar Resposta</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Detalhes</h4>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nº</dt>
                    <dd class="font-mono font-medium">{{ $ticket->ticket_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        @php $sc = $ticket->status_color; @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : ($sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                            {{ $ticket->status_label }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Prioridade</dt>
                    <dd>
                        @php $pc = $ticket->priority_color; @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $pc=='danger' ? 'bg-red-100 text-red-800' : ($pc=='warning' ? 'bg-amber-100 text-amber-800' : ($pc=='blue' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ $ticket->priority_label }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Categoria</dt>
                    <dd class="font-medium">{{ $ticket->category_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Criado por</dt>
                    <dd class="font-medium">{{ $ticket->creator?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Criado em</dt>
                    <dd class="font-medium">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($ticket->resolved_at)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Resolvido em</dt>
                        <dd class="font-medium">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
                @if($ticket->closed_at)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fechado em</dt>
                        <dd class="font-medium">{{ $ticket->closed_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Cliente</h4>
            <p class="font-medium text-sm">{{ $ticket->client->company_name }}</p>
            <p class="text-sm text-gray-500">{{ $ticket->client->email }}</p>
            <p class="text-sm text-gray-500">{{ $ticket->client->phone }}</p>
            <a href="{{ route('admin.clients.show', $ticket->client) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Cliente</a>

            @if($ticket->contract)
                <hr class="my-3">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Contrato</h5>
                <p class="text-sm">{{ $ticket->contract->contract_number }}</p>
                <a href="{{ route('admin.contracts.show', $ticket->contract) }}" class="text-primary-600 text-sm hover:underline">Ver Contrato</a>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Atribuição</h4>
            <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}">
                @csrf
                <div class="flex gap-2">
                    <select name="assigned_to" class="flex-1 rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Selecione...</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $ticket->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-primary-600 text-white px-3 py-2 rounded text-sm hover:bg-primary-700">Atribuir</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Ações</h4>
            <div class="flex flex-col gap-2">
                @if(!in_array($ticket->status, ['closed']))
                    <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}">
                        @csrf
                        <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700" onclick="return confirm('Fechar este chamado?')">Fechar Chamado</button>
                    </form>
                @endif
                @if(in_array($ticket->status, ['closed', 'resolved']))
                    <form method="POST" action="{{ route('admin.tickets.reopen', $ticket) }}">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Reabrir Chamado</button>
                    </form>
                @endif
                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Chamado</a>
            </div>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.tickets.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Chamados</a>
</div>
@endsection
