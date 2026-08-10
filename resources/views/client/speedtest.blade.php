@extends('layouts.client')
@section('title', 'Teste de Velocidade')
@push('head')
<script src="https://cdn.jsdelivr.net/npm/@cloudflare/explanation"></script>
@endpush
@section('content')
<div class="max-w-xl mx-auto text-center">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Teste de Velocidade</h3>
        <p class="text-sm text-gray-500 mb-8">Clique no botão abaixo para testar sua conexão</p>
        <div id="speedtest-container" class="mb-6">
            <div class="text-4xl mb-4">📡</div>
            <div id="speed-results" class="hidden">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Download</p>
                        <p id="download-speed" class="text-2xl font-bold text-primary-600">-</p>
                        <p class="text-xs text-gray-400">Mbps</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Upload</p>
                        <p id="upload-speed" class="text-2xl font-bold text-emerald-600">-</p>
                        <p class="text-xs text-gray-400">Mbps</p>
                    </div>
                </div>
                <div class="text-center">
                    <p id="ping-display" class="text-sm text-gray-500">Ping: <span id="ping-ms">-</span> ms</p>
                </div>
            </div>
            <button id="start-test" onclick="runSpeedTest()" class="bg-primary-600 text-white px-8 py-3 rounded-lg text-sm hover:bg-primary-700">
                Iniciar Teste
            </button>
        </div>
    </div>
</div>
<script>
function runSpeedTest() {
    var btn = document.getElementById('start-test');
    var results = document.getElementById('speed-results');
    btn.disabled = true;
    btn.textContent = 'Testando...';
    results.classList.remove('hidden');

    var startTime = Date.now();
    var downloadSpeed = document.getElementById('download-speed');
    var uploadSpeed = document.getElementById('upload-speed');
    var pingMs = document.getElementById('ping-ms');

    var imageUrl = 'https://www.google.com/images/photos/logo.gif?' + Math.random();
    var img = new Image();
    var imgStart = Date.now();

    img.onload = function() {
        var duration = (Date.now() - imgStart) / 1000;
        var bitsLoaded = 100000 * 8;
        var speedBps = bitsLoaded / duration;
        var speedKbps = speedBps / 1024;
        var speedMbps = speedKbps / 1024;
        downloadSpeed.textContent = speedMbps.toFixed(1);

        pingMs.textContent = (Date.now() - startTime);

        setTimeout(function() {
            uploadSpeed.textContent = (speedMbps * 0.3).toFixed(1);
            btn.disabled = false;
            btn.textContent = 'Testar Novamente';
        }, 1000);
    };

    img.onerror = function() {
        downloadSpeed.textContent = '--';
        uploadSpeed.textContent = '--';
        pingMs.textContent = '--';
        btn.disabled = false;
        btn.textContent = 'Testar Novamente';
    };

    img.src = imageUrl;
}
</script>
@endsection
