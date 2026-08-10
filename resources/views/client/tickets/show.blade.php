@extends('layouts.client')

@section('title', "Chamado {$ticket->ticket_number}")

@section('content')
    <div class="mb-6">
        <a href="{{ route('client.tickets.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">&larr; Voltar para Chamados</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Chamado {{ $ticket->ticket_number }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $ticket->subject }}</h2>
                <div class="flex flex-wrap gap-3 mb-6">
                    @php
                        $scolors = ['open' => 'bg-red-50 text-red-700 border-red-200', 'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200', 'waiting_client' => 'bg-blue-50 text-blue-700 border-blue-200', 'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'closed' => 'bg-gray-50 text-gray-600 border-gray-200'];
                        $pcolors = ['low' => 'bg-gray-50 text-gray-600 border-gray-200', 'medium' => 'bg-blue-50 text-blue-700 border-blue-200', 'high' => 'bg-amber-50 text-amber-700 border-amber-200', 'critical' => 'bg-red-50 text-red-700 border-red-200'];
                        $ccolors = ['technical' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'billing' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'commercial' => 'bg-purple-50 text-purple-700 border-purple-200', 'installation' => 'bg-cyan-50 text-cyan-700 border-cyan-200', 'complaint' => 'bg-rose-50 text-rose-700 border-rose-200', 'other' => 'bg-gray-50 text-gray-600 border-gray-200'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $scolors[$ticket->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->status_label }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $pcolors[$ticket->priority] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->priority_label }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $ccolors[$ticket->category] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->category_label }}</span>
                </div>

                <div class="space-y-4" id="messages">
                    @forelse($ticket->messages as $message)
                        <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $message->user_id === auth()->id() ? 'bg-primary-50 border-primary-200' : 'bg-gray-50 border-gray-200' }} border rounded-xl px-4 py-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium text-gray-700">{{ $message->user?->name ?? 'Sistema' }}</span>
                                    <span class="text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                    @if($message->is_system)
                                        <span class="text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">sistema</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                                @if($message->attachments)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach((array)$message->attachments as $attachment)
                                            <a href="#" class="text-xs text-primary-600 hover:text-primary-800 underline">📎 {{ $attachment }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Nenhuma mensagem neste chamado.</p>
                    @endforelse
                </div>
            </div>

            @if(in_array($ticket->status, ['open', 'in_progress', 'waiting_client']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Responder</h2>
                    <form method="POST" action="{{ route('client.tickets.reply', $ticket) }}">
                        @csrf
                        <div>
                            <textarea name="message" rows="4" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('message') border-red-500 @enderror" placeholder="Digite sua resposta...">{{ old('message') }}</textarea>
                            @error('message') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm">Enviar Resposta</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalhes</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500">Número</p>
                        <p class="font-medium text-gray-900">{{ $ticket->ticket_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $scolors[$ticket->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->status_label }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Prioridade</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $pcolors[$ticket->priority] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->priority_label }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Categoria</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $ccolors[$ticket->category] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->category_label }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500">Criado em</p>
                        <p class="font-medium text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($ticket->resolved_at)
                        <div>
                            <p class="text-gray-500">Resolvido em</p>
                            <p class="font-medium text-gray-900">{{ $ticket->resolved_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    @if($ticket->closed_at)
                        <div>
                            <p class="text-gray-500">Fechado em</p>
                            <p class="font-medium text-gray-900">{{ $ticket->closed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    @if($ticket->assignedTo)
                        <div>
                            <p class="text-gray-500">Responsável</p>
                            <p class="font-medium text-gray-900">{{ $ticket->assignedTo->name }}</p>
                        </div>
                    @endif
                    @if($ticket->contract)
                        <div>
                            <p class="text-gray-500">Contrato</p>
                            <p class="font-medium text-gray-900">{{ $ticket->contract->contract_number }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(in_array($ticket->status, ['open', 'in_progress', 'waiting_client']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Ações</h2>
                    <form method="POST" action="{{ route('client.tickets.close', $ticket) }}" onsubmit="return confirm('Tem certeza que deseja fechar este chamado?')">
                        @csrf
                        <button type="submit" class="w-full bg-red-50 text-red-700 border border-red-200 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition">Fechar Chamado</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection