@extends('layouts.client')
@section('title', 'Notificações')
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">{{ $notifications->total() }} notificações</p>
        <form method="POST" action="{{ route('client.notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-primary-600 hover:text-primary-800 text-sm">Marcar todas como lidas</button>
        </form>
    </div>
    <div class="divide-y">
        @forelse($notifications as $notification)
        <div class="p-4 flex items-start gap-3 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
            <div class="flex-1">
                <p class="text-sm {{ $notification->is_read ? 'text-gray-600' : 'text-gray-900 font-medium' }}">
                    {{ $notification->data['message'] ?? 'Notificação' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notification->is_read)
            <form method="POST" action="{{ route('client.notifications.read', $notification) }}">
                @csrf
                <button type="submit" class="text-xs text-primary-600 hover:underline">Ler</button>
            </form>
            @endif
        </div>
        @empty
        <p class="p-6 text-center text-gray-400">Nenhuma notificação</p>
        @endforelse
    </div>
    <div class="p-5 border-t">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
