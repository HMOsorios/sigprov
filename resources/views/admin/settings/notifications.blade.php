@extends('layouts.admin')

@section('title', 'Notificações')
@section('page-title', 'Notificações')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-600">
        {{ $unreadCount }} notificação{{ $unreadCount != 1 ? 'ões' : '' }} não {{ $unreadCount != 1 ? 'lidas' : 'lida' }}
    </p>
    @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.settings.notifications.read-all') }}">
            @csrf
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Marcar Todas como Lidas</button>
        </form>
    @endif
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Título</th>
                    <th class="p-3 font-medium">Mensagem</th>
                    <th class="p-3 font-medium">Data</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                    <tr class="border-t hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                        <td class="p-3">
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                            @else
                                <span class="w-2 h-2 bg-gray-300 rounded-full inline-block"></span>
                            @endif
                        </td>
                        <td class="p-3 font-medium">{{ $notification->title }}</td>
                        <td class="p-3 max-w-[300px] truncate">{{ $notification->message }}</td>
                        <td class="p-3">{{ $notification->created_at ? $notification->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-3 text-right">
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('admin.settings.notifications.read', $notification) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-primary-600 hover:text-primary-800 text-sm">Marcar como Lida</button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm">Lida</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="5">Nenhuma notificação encontrada</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $notifications])
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Configurações</a>
</div>
@endsection
