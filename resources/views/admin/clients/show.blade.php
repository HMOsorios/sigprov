@extends('layouts.admin')

@section('title', $client->company_name)
@section('page-title', $client->company_name)

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-emerald-500">
        <p class="text-sm text-gray-500">Contratos Ativos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $activeContracts }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Faturado</p>
        <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalInvoiced, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Chamados Abertos</p>
        <p class="text-2xl font-bold text-gray-800">{{ $openTickets }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-gray-500">
        <p class="text-sm text-gray-500">Status</p>
        @php $s = $client->status; @endphp
        <span class="px-2 py-1 text-xs rounded-full {{ $s=='active' ? 'bg-emerald-100 text-emerald-800' : ($s=='inactive' ? 'bg-gray-100 text-gray-800' : ($s=='blocked' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">
            {{ $s=='active' ? 'Ativo' : ($s=='inactive' ? 'Inativo' : ($s=='blocked' ? 'Bloqueado' : 'Cancelado')) }}
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Cliente</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Razão Social</dt><dd class="font-medium">{{ $client->company_name }}</dd></div>
            <div><dt class="text-gray-500">Nome Fantasia</dt><dd class="font-medium">{{ $client->fantasy_name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">CPF/CNPJ</dt><dd class="font-medium">{{ $client->document_formatted }}</dd></div>
            <div><dt class="text-gray-500">RG/IE</dt><dd class="font-medium">{{ $client->rg_ie ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium">{{ $client->person_type == 'pj' ? 'Jurídica' : 'Física' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $client->email }}</dd></div>
            <div><dt class="text-gray-500">Telefone</dt><dd class="font-medium">{{ $client->phone }}</dd></div>
            <div><dt class="text-gray-500">Celular</dt><dd class="font-medium">{{ $client->cellphone ?? '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Endereço</dt><dd class="font-medium">{{ $client->address_full }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $client->observations ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Contato</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $client->contact_name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Telefone</dt><dd class="font-medium">{{ $client->contact_phone ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $client->contact_email ?? '-' }}</dd></div>
        </dl>

        <hr class="my-4">

        <div class="flex flex-col gap-2">
            @if($client->status !== 'blocked')
                <form method="POST" action="{{ route('admin.clients.block', $client) }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700" onclick="return confirm('Bloquear cliente?')">Bloquear Cliente</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.clients.unblock', $client) }}">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Desbloquear Cliente</button>
                </form>
            @endif
            <a href="{{ route('admin.clients.edit', $client) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Cliente</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Contratos</h3>
            <a href="{{ route('admin.contracts.create') }}?client_id={{ $client->id }}" class="text-sm text-primary-600 hover:underline">Novo</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Nº</th><th class="pb-2">Plano</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($client->contracts as $contract)
                        <tr class="border-b last:border-0">
                            <td class="py-2"><a href="{{ route('admin.contracts.show', $contract) }}" class="text-primary-600 hover:underline">{{ $contract->contract_number }}</a></td>
                            <td class="py-2">{{ $contract->plan->name ?? '-' }}</td>
                            <td class="py-2">{{ $contract->status_label }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum contrato</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Links</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">PPPoE</th><th class="pb-2">IP</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($links as $link)
                        <tr class="border-b last:border-0">
                            <td class="py-2"><a href="{{ route('admin.links.show', $link) }}" class="text-primary-600 hover:underline">{{ $link->pppoe_user ?? '-' }}</a></td>
                            <td class="py-2">{{ $link->ip_address ?? '-' }}</td>
                            <td class="py-2">{{ $link->status_label }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum link</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Faturas</h3>
            <a href="{{ route('admin.invoices.create') }}?client_id={{ $client->id }}" class="text-sm text-primary-600 hover:underline">Nova</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Nº</th><th class="pb-2">Valor</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($client->invoices as $invoice)
                        <tr class="border-b last:border-0">
                            <td class="py-2"><a href="{{ route('admin.invoices.show', $invoice) }}" class="text-primary-600 hover:underline">{{ $invoice->invoice_number }}</a></td>
                            <td class="py-2">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                            <td class="py-2">{{ $invoice->status_label }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhuma fatura</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Chamados</h3>
            <a href="{{ route('admin.tickets.create') }}?client_id={{ $client->id }}" class="text-sm text-primary-600 hover:underline">Novo</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Nº</th><th class="pb-2">Assunto</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($client->tickets as $ticket)
                        <tr class="border-b last:border-0">
                            <td class="py-2"><a href="{{ route('admin.tickets.show', $ticket) }}" class="text-primary-600 hover:underline">{{ $ticket->ticket_number }}</a></td>
                            <td class="py-2">{{ $ticket->subject }}</td>
                            <td class="py-2">{{ $ticket->status_label }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum chamado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Clientes</a>
</div>
@endsection
