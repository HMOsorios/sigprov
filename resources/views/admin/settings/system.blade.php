@extends('layouts.admin')

@section('title', 'Informações do Sistema')
@section('page-title', 'Informações do Sistema')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações do Sistema</h3>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">PHP Version</dt>
            <dd class="text-lg font-semibold text-gray-800">{{ $phpVersion }}</dd>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Laravel Version</dt>
            <dd class="text-lg font-semibold text-gray-800">{{ $laravelVersion }}</dd>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Environment</dt>
            <dd class="text-lg font-semibold text-gray-800">{{ $environment }}</dd>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Debug Mode</dt>
            <dd>
                <span class="px-2 py-1 text-xs rounded-full {{ $debugMode ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $debugMode ? 'Ativado' : 'Desativado' }}
                </span>
            </dd>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">DB Connection</dt>
            <dd class="text-lg font-semibold text-gray-800">{{ $dbConnection }}</dd>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <dt class="text-gray-500 text-xs uppercase tracking-wider mb-1">Timezone</dt>
            <dd class="text-lg font-semibold text-gray-800">{{ config('app.timezone') }}</dd>
        </div>
    </dl>
</div>

<div class="mt-6">
    <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Configurações</a>
</div>
@endsection
