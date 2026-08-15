<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
        @yield('breadcrumb')
    </div>
    <div class="flex items-center gap-4">
        @php
            $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        @endphp
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition" title="Página inicial">
            <span class="text-xl">🏠</span>
        </a>
        <a href="{{ route('admin.settings.notifications') }}" class="relative text-gray-500 hover:text-gray-700 transition">
            <span class="text-xl">🔔</span>
            @if($unreadNotifications > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
            @endif
        </a>
        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 text-sm hover:opacity-80 transition" title="Meu Perfil">
            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div class="hidden sm:block">
                <p class="font-medium text-gray-700">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->role?->label }}</p>
            </div>
        </a>
    </div>
</header>
