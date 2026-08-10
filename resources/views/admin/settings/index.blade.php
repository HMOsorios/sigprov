@extends('layouts.admin')

@section('title', 'Configurações')
@section('page-title', 'Configurações')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Configurações Gerais</h3>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Aplicação</label>
                <input type="text" name="app_name" value="{{ config('app.name') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL da Aplicação</label>
                <input type="url" name="app_url" value="{{ config('app.url') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar Configurações</button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="{{ route('admin.settings.system') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition border-l-4 border-blue-500">
        <div class="text-2xl mb-2">🖥️</div>
        <h4 class="font-semibold text-gray-800">Informações do Sistema</h4>
        <p class="text-sm text-gray-500">PHP, Laravel, ambiente, banco de dados</p>
    </a>
    <a href="{{ route('admin.settings.audit') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition border-l-4 border-violet-500">
        <div class="text-2xl mb-2">📋</div>
        <h4 class="font-semibold text-gray-800">Logs de Auditoria</h4>
        <p class="text-sm text-gray-500">Histórico de ações realizadas no sistema</p>
    </a>
    <a href="{{ route('admin.settings.notifications') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition border-l-4 border-amber-500">
        <div class="text-2xl mb-2">🔔</div>
        <h4 class="font-semibold text-gray-800">Notificações</h4>
        <p class="text-sm text-gray-500">Visualizar e gerenciar notificações</p>
    </a>
    <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-gray-500">
        <div class="text-2xl mb-2">🗑️</div>
        <h4 class="font-semibold text-gray-800">Cache</h4>
        <p class="text-sm text-gray-500 mb-3">Limpar cache da aplicação</p>
        <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
            @csrf
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700" onclick="return confirm('Limpar cache?')">Limpar Cache</button>
        </form>
    </div>
</div>
@endsection
