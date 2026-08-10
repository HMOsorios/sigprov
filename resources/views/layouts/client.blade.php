<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SisProv Cliente</title>
    <meta name="description" content="SisProv - Área do Cliente">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
    @stack('head')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 text-gray-900 font-bold text-lg">
                        <span class="text-2xl">🌐</span>
                        <span>SisProv</span>
                    </a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('client.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('client.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Dashboard</a>
                        <a href="{{ route('client.invoices.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('client.invoices.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Faturas</a>
                        <a href="{{ route('client.tickets.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('client.tickets.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">Chamados</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @php
                        $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="#" class="relative text-gray-500 hover:text-gray-700 transition">
                        <span class="text-xl">🔔</span>
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
                        @endif
                    </a>
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm bg-white rounded-full p-1.5 hover:bg-gray-50 border border-gray-200 transition">
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            <div class="hidden sm:block text-left pr-1">
                                <p class="font-medium text-gray-700 text-sm leading-tight">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 leading-tight">{{ auth()->user()->role?->label }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50" style="display:none">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
        </div>
    </main>

    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')
</body>
</html>