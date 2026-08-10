@extends('layouts.admin')

@section('title', 'Link '.($link->pppoe_user ?? '#'.$link->id))
@section('page-title', 'Link '.($link->pppoe_user ?? '#'.$link->id))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalhes do Link</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Usuário PPPoE</dt><dd class="font-medium font-mono">{{ $link->pppoe_user ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">IP</dt><dd class="font-medium font-mono">{{ $link->ip_address ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">MAC Address</dt><dd class="font-medium font-mono">{{ $link->mac_address ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">VLAN</dt><dd class="font-medium">{{ $link->vlan ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">ONT Serial</dt><dd class="font-medium font-mono">{{ $link->ont_serial ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">ONT Marca</dt><dd class="font-medium">{{ $link->ont_brand ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">ONT Modelo</dt><dd class="font-medium">{{ $link->ont_model ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Cabo Origem</dt><dd class="font-medium">{{ $link->cable_origin ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Cabo Drop</dt><dd class="font-medium">{{ $link->cable_drop ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Splitter</dt><dd class="font-medium">{{ $link->splitter_location ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Ativado em</dt><dd class="font-medium">{{ $link->activated_at ? $link->activated_at->format('d/m/Y') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd>
                @php $sc = $link->status_color; @endphp
                <span class="px-2 py-0.5 text-xs rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : ($sc=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ $link->status_label }}
                </span>
            </dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="font-medium">{{ $link->notes ?? '-' }}</dd></div>
        </dl>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Sinal Óptico</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">RX</span>
                    <span class="font-medium {{ ($link->signal_rx ?? 0) < -25 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $link->signal_rx ?? '-' }} dBm
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">TX</span>
                    <span class="font-medium">{{ $link->signal_tx ?? '-' }} dBm</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Contrato</h4>
            @if($link->contract)
                <p class="text-sm font-medium">{{ $link->contract->contract_number }}</p>
                <p class="text-sm text-gray-500">{{ $link->contract->client->company_name }}</p>
                <p class="text-sm text-gray-500">{{ $link->contract->plan->name ?? '-' }}</p>
                <a href="{{ route('admin.contracts.show', $link->contract) }}" class="text-primary-600 text-sm hover:underline mt-2 inline-block">Ver Contrato</a>
            @else
                <p class="text-sm text-gray-400">Nenhum contrato vinculado</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Ações</h4>
            <div class="flex flex-col gap-2">
                @if($link->status !== 'blocked')
                    <form method="POST" action="{{ route('admin.links.block', $link) }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700" onclick="return confirm('Bloquear este link?')">Bloquear Link</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.links.unblock', $link) }}">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Desbloquear Link</button>
                    </form>
                @endif
                <a href="{{ route('admin.links.edit', $link) }}" class="w-full text-center bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Link</a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Logs do Link</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Ação</th>
                    <th class="pb-2">Descrição</th>
                    <th class="pb-2">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($link->logs as $log)
                    <tr class="border-b last:border-0">
                        <td class="py-2">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $log->action=='blocked' ? 'bg-red-100 text-red-800' : ($log->action=='unblocked' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-2">{{ $log->description }}</td>
                        <td class="py-2">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum log encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.links.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Links</a>
</div>
@endsection
