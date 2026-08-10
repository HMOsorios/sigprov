<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SisProv') - Sistema de Gestão para Provedores</title>
    <meta name="description" content="SisProv - Sistema completo para gestão de provedores de internet. Gerencie clientes, planos, contratos, servidores e muito mais.">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <main class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="text-5xl mb-3">🌐</div>
                <h1 class="text-3xl font-extrabold text-gray-900">SisProv</h1>
                <p class="mt-2 text-sm text-gray-600">Gestão inteligente para provedores de internet</p>
            </div>
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
