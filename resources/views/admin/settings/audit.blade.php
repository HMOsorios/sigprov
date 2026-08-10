@extends('layouts.admin')

@section('title', 'Logs de Auditoria')
@section('page-title', 'Logs de Auditoria')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b">
        <p class="text-sm text-gray-600">Total: {{ $logs->total() }} registros</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Usuário</th>
                    <th class="p-3 font-medium">Ação</th>
                    <th class="p-3 font-medium">Entidade</th>
                    <th class="p-3 font-medium">Descrição</th>
                    <th class="p-3 font-medium">Data</th>
                    <th class="p-3 font-medium">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $log->user?->name ?? '-' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $log->action=='create' ? 'bg-emerald-100 text-emerald-800' : ($log->action=='update' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-3">{{ $log->entity_type }}</td>
                        <td class="p-3 max-w-[300px] truncate">{{ $log->description }}</td>
                        <td class="p-3">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-3 font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="6">Nenhum registro de auditoria encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $logs])
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Configurações</a>
</div>
@endsection
