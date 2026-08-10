@extends('layouts.admin')
@section('title', $lead->name)
@section('page-title', $lead->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Lead</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $lead->name }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $lead->email }}</dd></div>
            <div><dt class="text-gray-500">Telefone</dt><dd class="font-medium">{{ $lead->phone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Celular</dt><dd class="font-medium">{{ $lead->cellphone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Origem</dt>
                <dd><span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800">{{ $lead->source ?? '-' }}</span></dd>
            </div>
            <div><dt class="text-gray-500">Status</dt>
                <dd>
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
                </dd>
            </div>
            <div><dt class="text-gray-500">Responsável</dt><dd class="font-medium">{{ $lead->assignedTo?->name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Plano de Interesse</dt><dd class="font-medium">{{ $lead->interest_plan ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Endereço</dt><dd class="font-medium">{{ $lead->address ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Cidade/Estado</dt><dd class="font-medium">{{ $lead->city }}{{ $lead->state ? '/'.$lead->state : '' }}</dd></div>
            <div><dt class="text-gray-500">Criado em</dt><dd class="font-medium">{{ $lead->created_at->format('d/m/Y H:i') }}</dd></div>
        </dl>

        @if($lead->notes)
        <hr class="my-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Observações</h4>
        <p class="text-sm text-gray-600">{{ $lead->notes }}</p>
        @endif

        @if($lead->convertedClient)
        <hr class="my-4">
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded">
            <p class="text-sm font-semibold text-emerald-800">Convertido para Cliente</p>
            <a href="{{ route('admin.clients.show', $lead->convertedClient) }}" class="text-sm text-primary-600 hover:underline">{{ $lead->convertedClient->company_name }}</a>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações</h3>
        <div class="flex flex-col gap-2">
            <a href="{{ route('admin.leads.edit', $lead) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar</a>
            @if(!$lead->convertedClient && $lead->status !== 'lost')
            <a href="{{ route('admin.leads.convert', $lead) }}" class="w-full text-center bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Converter em Cliente</a>
            @endif
            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="w-full" onsubmit="return confirm('Excluir este lead?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">Excluir</button>
            </form>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.leads.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Leads</a>
</div>
@endsection
