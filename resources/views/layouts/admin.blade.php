<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SisProv Admin</title>
    <meta name="description" content="SisProv - Painel Administrativo">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}.sidebar-transition{transition:all .3s ease}</style>
    @stack('head')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        @include('components.admin-sidebar')
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('components.admin-header')
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @if(session('success'))
                    <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-4 py-3 rounded shadow-sm flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded shadow-sm flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 px-4 py-3 rounded shadow-sm flex items-center justify-between">
                        <span>{{ session('warning') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-800">&times;</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded shadow-sm">
                        <ul class="list-disc ml-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
