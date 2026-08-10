@extends('layouts.admin')
@section('title', $ipPool->name)
@section('page-title', $ipPool->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalhes do Pool</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $ipPool->name }}</dd></div>
            <div><dt class="text-gray-500">Subnet</dt><dd class="font-medium font-mono">{{ $ipPool->subnet }}</dd></div>
            <div><dt class="text-gray-500">Range</dt><dd class="font-medium font-mono">{{ $ipPool->range }}</dd></div>
            <div><dt class="text-gray-500">Gateway</dt><dd class="font-medium font-mono">{{ $ipPool->gateway }}</dd></div>
            <div><dt class="text-gray-500">DNS 1</dt><dd class="font-medium">{{ $ipPool->dns1 ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">DNS 2</dt><dd class="font-medium">{{ $ipPool->dns2 ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium">
                <span class="px-2 py-0.5 text-xs rounded-full {{ $ipPool->type=='ipv4' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ strtoupper($ipPool->type) }}
                </span>
            </dd></div>
            <div><dt class="text-gray-500">CGNAT</dt><dd class="font-medium">{{ $ipPool->is_cgnat ? 'Sim' : 'Não' }}</dd></div>
            <div><dt class="text-gray-500">Servidor</dt><dd class="font-medium">{{ $ipPool->server?->name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $ipPool->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $ipPool->status=='active' ? 'Ativo' : 'Inativo' }}
                </span>
            </dd></div>
        </dl>
        @if($ipPool->notes)
            <div class="mt-4"><dt class="text-sm text-gray-500">Observações</dt><dd class="text-sm">{{ $ipPool->notes }}</dd></div>
        @endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Utilização</h4>
            @php $usage = $ipPool->usage_percent; @endphp
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl font-bold {{ $usage < 70 ? 'text-emerald-600' : ($usage <= 90 ? 'text-amber-600' : 'text-red-600') }}">{{ $usage }}%</span>
                <span class="text-sm text-gray-500">({{ $ipPool->used }}/{{ $ipPool->total }})</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full {{ $usage < 70 ? 'bg-emerald-500' : ($usage <= 90 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $usage }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-5">
            <a href="{{ route('admin.ip-pools.edit', $ipPool) }}" class="w-full text-center block bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Pool</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Atribuições Ativas ({{ $ipPool->activeAssignments->count() }})</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">IP</th>
                    <th class="pb-2">MAC</th>
                    <th class="pb-2">Cliente</th>
                    <th class="pb-2">Link</th>
                    <th class="pb-2">Atribuído em</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ipPool->activeAssignments as $assignment)
                    <tr class="border-b last:border-0">
                        <td class="py-2 font-mono text-xs">{{ $assignment->ip_address }}</td>
                        <td class="py-2 font-mono text-xs">{{ $assignment->mac_address ?? '-' }}</td>
                        <td class="py-2">
                            @php $client = $assignment->link?->contract?->client; @endphp
                            @if($client)
                                <a href="{{ route('admin.clients.show', $client) }}" class="text-primary-600 hover:underline">{{ $client->name_display }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-2">
                            @if($assignment->link)
                                <a href="{{ route('admin.links.show', $assignment->link) }}" class="text-primary-600 hover:underline">#{{ $assignment->link_id }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-2">{{ $assignment->assigned_at ? $assignment->assigned_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="5">Nenhuma atribuição ativa</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.ip-pools.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Pools</a>
</div>
@endsection
