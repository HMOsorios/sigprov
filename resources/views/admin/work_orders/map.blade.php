@extends('layouts.admin')
@section('title', 'Mapa de OS')
@section('page-title', 'Mapa de Ordens de Serviço')
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
    <div class="p-4 text-sm text-gray-500 bg-amber-50 border-l-4 border-amber-400">
        Configure as coordenadas (latitude/longitude) no cadastro do cliente para exibição correta no mapa.
    </div>
    <div id="map" style="height: 500px;"></div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">OS Agendadas</h3>
    <div class="divide-y text-sm">
        @forelse($orders as $order)
        <div class="py-2 flex items-center justify-between">
            <div>
                <span class="font-semibold">#{{ $order->id }}</span>
                <span class="text-gray-600 ml-2">{{ $order->client?->name_display ?? '-' }}</span>
                <span class="text-xs text-gray-400 ml-2">{{ $order->type_label }}</span>
            </div>
            <div class="text-xs text-gray-500">{{ $order->scheduled_at ? $order->scheduled_at->format('d/m/Y H:i') : '-' }}</div>
        </div>
        @empty
        <p class="py-4 text-center text-gray-400">Nenhuma OS agendada.</p>
        @endforelse
    </div>
</div>
@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-23.5505, -46.6333], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const orders = @json($orders->map(fn($o) => [
        'id' => $o->id,
        'client' => $o->client?->name_display ?? 'N/A',
        'type' => $o->type_label ?? '',
        'lat' => $o->client?->latitude ?? -23.5505,
        'lng' => $o->client?->longitude ?? -46.6333,
        'scheduled' => $o->scheduled_at?->format('d/m/Y H:i') ?? '',
    ]));

    orders.forEach(function(o) {
        if (o.lat && o.lng) {
            L.marker([o.lat, o.lng]).addTo(map)
                .bindPopup('<b>OS #' + o.id + '</b><br>' + o.client + '<br>' + o.type + (o.scheduled ? '<br>' + o.scheduled : ''));
        }
    });
</script>
@endpush
