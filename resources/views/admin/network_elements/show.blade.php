@extends('layouts.admin')
@section('title', $networkElement->name)
@section('page-title', $networkElement->name)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalhes do Elemento</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $networkElement->name }}</dd></div>
            <div>
                <dt class="text-gray-500">Tipo</dt>
                <dd>
                    <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $networkElement->type=='olt' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $networkElement->type=='splitter' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $networkElement->type=='cto' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $networkElement->type=='client' ? 'bg-emerald-100 text-emerald-800' : '' }}
                        {{ $networkElement->type=='drop' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $networkElement->type=='caixa' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $networkElement->type=='armario' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $networkElement->type=='backbone' ? 'bg-indigo-100 text-indigo-800' : '' }}
                        {{ !in_array($networkElement->type, ['olt','splitter','cto','client','drop','caixa','armario','backbone']) ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $networkElement->type_label }}
                    </span>
                </dd>
            </div>
            <div><dt class="text-gray-500">Identificador</dt><dd class="font-medium font-mono">{{ $networkElement->identifier ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Serial</dt><dd class="font-medium font-mono">{{ $networkElement->serial ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Modelo</dt><dd class="font-medium">{{ $networkElement->model ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Ordem</dt><dd class="font-medium">{{ $networkElement->order ?? '-' }}</dd></div>
            <div>
                <dt class="text-gray-500">Elemento Pai</dt>
                <dd>
                    @if($networkElement->parent)
                        <a href="{{ route('admin.network-elements.show', $networkElement->parent) }}" class="text-primary-600 hover:underline">{{ $networkElement->parent->name }}</a>
                    @else
                        -
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-500">Servidor</dt><dd class="font-medium">{{ $networkElement->server?->name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Endereço</dt><dd class="font-medium">{{ $networkElement->address ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Cidade/UF</dt><dd class="font-medium">{{ $networkElement->city ? $networkElement->city . ($networkElement->state ? '/'.$networkElement->state : '') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Coordenadas</dt>
                <dd>
                    @if($networkElement->latitude && $networkElement->longitude)
                        <span class="font-mono text-xs">{{ $networkElement->latitude }}, {{ $networkElement->longitude }}</span>
                    @else
                        -
                    @endif
                </dd>
            </div>
            <div><dt class="text-gray-500">Status</dt>
                <dd>
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $networkElement->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $networkElement->status_label }}
                    </span>
                </dd>
            </div>
        </dl>
        @if($networkElement->notes)
            <div class="mt-4"><dt class="text-sm text-gray-500">Observações</dt><dd class="text-sm">{{ $networkElement->notes }}</dd></div>
        @endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <a href="{{ route('admin.network-elements.edit', $networkElement) }}" class="w-full text-center block bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Editar Elemento</a>
        </div>

        @if($networkElement->latitude && $networkElement->longitude)
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Mapa</h4>
            <div id="elementMap" style="height: 200px;" class="rounded border"></div>
            @push('head')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            @endpush
            @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                var map = L.map('elementMap').setView([{{ $networkElement->latitude }}, {{ $networkElement->longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                L.marker([{{ $networkElement->latitude }}, {{ $networkElement->longitude }}]).addTo(map)
                    .bindPopup('<strong>{{ $networkElement->name }}</strong><br>{{ $networkElement->type_label }}');
            </script>
            @endpush
        </div>
        @endif
    </div>
</div>

@if($networkElement->children->count() > 0)
<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Elementos Filhos ({{ $networkElement->children->count() }})</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Nome</th>
                    <th class="pb-2">Tipo</th>
                    <th class="pb-2">Identificador</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($networkElement->children as $child)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="py-2 font-medium">{{ $child->name }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                {{ $child->type=='olt' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $child->type=='splitter' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $child->type=='cto' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $child->type=='client' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $child->type=='drop' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $child->type=='caixa' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $child->type=='armario' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $child->type=='backbone' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                {{ $child->type_label }}
                            </span>
                        </td>
                        <td class="py-2 font-mono text-xs">{{ $child->identifier ?? '-' }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $child->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">{{ $child->status_label }}</span>
                        </td>
                        <td class="py-2">
                            <a href="{{ route('admin.network-elements.show', $child) }}" class="text-primary-600 hover:underline text-sm">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="mt-6">
    <a href="{{ route('admin.network-elements.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Elementos de Rede</a>
</div>
@endsection
