@extends('layouts.admin')

@section('title', 'Contrato '.$contract->contract_number)
@section('page-title', 'Contrato '.$contract->contract_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados do Contrato</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nº Contrato</dt><dd class="font-medium">{{ $contract->contract_number }}</dd></div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd>
                    @php $sc = $contract->status_color; @endphp
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $contract->status_label }}
                    </span>
                </dd>
            </div>
            <div><dt class="text-gray-500">Data de Início</dt><dd class="font-medium">{{ $contract->start_date->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-500">Data de Término</dt><dd class="font-medium">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Dia Vencimento</dt><dd class="font-medium">{{ $contract->due_day }}</dd></div>
            <div><dt class="text-gray-500">Valor</dt><dd class="font-medium">R$ {{ number_format($contract->signed_price, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Desconto</dt><dd class="font-medium">{{ $contract->discount_type == 'percent' ? $contract->discount_percent.'%' : 'R$ '.number_format($contract->discount_value ?? 0, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Valor Efetivo</dt><dd class="font-medium">R$ {{ number_format($contract->effective_price, 2, ',', '.') }}</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Cliente</h3>
        <p class="font-medium text-sm">{{ $contract->client->company_name }}</p>
        <p class="text-sm text-gray-500">{{ $contract->client->cpf_cnpj }}</p>
        <p class="text-sm text-gray-500">{{ $contract->client->email }}</p>
        <p class="text-sm text-gray-500">{{ $contract->client->phone }}</p>
        <a href="{{ route('admin.clients.show', $contract->client) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Cliente</a>

        <hr class="my-4">
        <h4 class="text-md font-semibold text-gray-700 mb-2">Plano</h4>
        <p class="font-medium text-sm">{{ $contract->plan->name ?? '-' }}</p>
        <p class="text-sm text-gray-500">{{ $contract->plan->speed_label ?? '' }}</p>
        <p class="text-sm text-gray-500">{{ $contract->plan->price_formatted ?? '' }}</p>

        <hr class="my-4">
        <div class="flex flex-col gap-2">
            @if($contract->status === 'active')
                <form method="POST" action="{{ route('admin.contracts.suspend', $contract) }}">
                    @csrf
                    <button type="submit" class="w-full bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700" onclick="return confirm('Suspender contrato?')">Suspender</button>
                </form>
            @elseif($contract->status === 'suspended')
                <form method="POST" action="{{ route('admin.contracts.reactivate', $contract) }}">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Reativar</button>
                </form>
            @endif
            <a href="{{ route('admin.contracts.edit', $contract) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Contrato</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Faturas</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Nº</th><th class="pb-2">Valor</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($contract->invoices as $invoice)
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
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Links</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">PPPoE</th><th class="pb-2">IP</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($contract->links as $link)
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

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Chamados</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Nº</th><th class="pb-2">Assunto</th><th class="pb-2">Status</th></tr></thead>
                <tbody>
                    @forelse($contract->tickets as $ticket)
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
    <a href="{{ route('admin.contracts.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Contratos</a>
</div>
@endsection
