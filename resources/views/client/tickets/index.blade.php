@extends('layouts.client')

@section('title', 'Meus Chamados')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Meus Chamados</h1>
            <p class="text-gray-500 mt-1">Acompanhe seus chamados de suporte.</p>
        </div>
        <a href="{{ route('client.tickets.create') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm">
            <span>+</span> Novo Chamado
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Abertos</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $openCount }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-2xl">📌</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Resolvidos</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $resolvedCount }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center text-2xl">✅</div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Fechados</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $closedCount }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center text-2xl">📁</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">Nº</th>
                        <th class="px-5 py-3 font-medium">Assunto</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Prioridade</th>
                        <th class="px-5 py-3 font-medium">Data</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('client.tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-800 font-medium">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 max-w-[250px] truncate">{{ $ticket->subject }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $scolors = ['open' => 'bg-red-50 text-red-700 border-red-200', 'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200', 'waiting_client' => 'bg-blue-50 text-blue-700 border-blue-200', 'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'closed' => 'bg-gray-50 text-gray-600 border-gray-200'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $scolors[$ticket->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->status_label }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $pcolors = ['low' => 'bg-gray-50 text-gray-600 border-gray-200', 'medium' => 'bg-blue-50 text-blue-700 border-blue-200', 'high' => 'bg-amber-50 text-amber-700 border-amber-200', 'critical' => 'bg-red-50 text-red-700 border-red-200'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $pcolors[$ticket->priority] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">{{ $ticket->priority_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('client.tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Abrir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">Nenhum chamado encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection