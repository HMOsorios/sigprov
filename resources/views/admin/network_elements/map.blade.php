@extends('layouts.admin')
@section('title', 'Mapa de Rede')
@section('page-title', 'Mapa de Rede')
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #networkMap { height: calc(100vh - 180px); }
</style>
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div id="networkMap"></div>
</div>

<div class="mt-3 flex flex-wrap gap-3 text-xs">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> OLT</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span> Splitter</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> CTO</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Cliente</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span> Drop</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span> Armário</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-500 inline-block"></span> Outros</span>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('networkMap').setView([-23.5505, -46.6333], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var typeColors = {
        'olt': 'red',
        'splitter': 'orange',
        'cto': 'blue',
        'client': 'green',
        'drop': 'yellow',
        'caixa': 'gray',
        'armario': 'purple',
        'backbone': 'indigo'
    };

    var markerColor = function(type) {
        var c = typeColors[type] || 'gray';
        var colors = {
            red: '#ef4444', orange: '#f97316', blue: '#3b82f6',
            green: '#22c55e', yellow: '#eab308', gray: '#6b7280',
            purple: '#a855f7', indigo: '#6366f1'
        };
        return colors[c] || '#6b7280';
    };

    var data = {!! $geoJson !!};

    var markers = [];

    data.features.forEach(function(f) {
        if (f.geometry.type === 'Point') {
            var coords = f.geometry.coordinates;
            var color = markerColor(f.properties.type);
            var icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background:' + color + ';width:14px;height:14px;border-radius:50%;border:2px solid white;box-shadow:0 1px 3px rgba(0,0,0,0.3)"></div>',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });
            var marker = L.marker([coords[1], coords[0]], { icon: icon }).addTo(map);
            marker.bindPopup(
                '<strong>' + f.properties.name + '</strong><br>' +
                (f.properties.type_label || f.properties.type) +
                (f.properties.identifier ? '<br><small>' + f.properties.identifier + '</small>' : '') +
                (f.properties.address ? '<br><small>' + f.properties.address + '</small>' : '')
            );
            markers.push(marker);
        } else if (f.geometry.type === 'LineString') {
            var coords = f.geometry.coordinates.map(function(c) {
                return [c[1], c[0]];
            });
            L.polyline(coords, {
                color: f.properties.stroke || '#94a3b8',
                weight: f.properties['stroke-width'] || 1.5,
                opacity: 0.6
            }).addTo(map);
        }
    });

    if (markers.length > 0) {
        var group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
</script>
@endpush
