@extends('layouts.admin')

@section('title', $server->name)
@section('page-title', $server->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalhes do Servidor</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Nome</dt><dd class="font-medium">{{ $server->name }}</dd></div>
            <div><dt class="text-gray-500">Hostname</dt><dd class="font-medium">{{ $server->hostname }}</dd></div>
            <div><dt class="text-gray-500">IP</dt><dd class="font-medium font-mono">{{ $server->ip_address }}</dd></div>
            <div><dt class="text-gray-500">Porta</dt><dd class="font-medium">{{ $server->port }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium">{{ $server->type_label }}</dd></div>
            <div><dt class="text-gray-500">Marca</dt><dd class="font-medium">{{ $server->brand ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Modelo</dt><dd class="font-medium">{{ $server->model ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Firmware</dt><dd class="font-medium">{{ $server->firmware_version ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Localização</dt><dd class="font-medium">{{ $server->location ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Monitorado</dt><dd class="font-medium">{{ $server->is_monitored ? 'Sim' : 'Não' }}</dd></div>
            <div><dt class="text-gray-500">Último Ping</dt><dd class="font-medium">{{ $server->last_ping_at ? $server->last_ping_at->format('d/m/Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Usuário</dt><dd class="font-medium">{{ $server->username ?? '-' }}</dd></div>
        </dl>
        @if($server->notes)
            <div class="mt-4"><dt class="text-sm text-gray-500">Observações</dt><dd class="text-sm">{{ $server->notes }}</dd></div>
        @endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-gray-700">Status</h4>
                @php $sc = $server->status_color; @endphp
                <span class="px-3 py-1 text-sm rounded-full {{ $sc=='success' ? 'bg-emerald-100 text-emerald-800' : ($sc=='danger' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $server->status_label }}
                </span>
            </div>
            <form method="POST" action="{{ route('admin.servers.ping', $server) }}">
                @csrf
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Testar Ping</button>
            </form>
            <a href="{{ route('admin.servers.edit', $server) }}" class="w-full text-center block mt-2 bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200">Editar Servidor</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h4 class="text-md font-semibold text-gray-700 mb-4">Desempenho</h4>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">CPU</span>
                        <span>{{ $server->cpu_usage ?? '-' }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width:{{ $server->cpu_usage ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Memória</span>
                        <span>{{ $server->memory_usage ?? '-' }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width:{{ $server->memory_usage ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Disco</span>
                        <span>{{ $server->disk_usage ?? '-' }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width:{{ $server->disk_usage ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Logs do Servidor</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2">Tipo</th>
                    <th class="pb-2">Mensagem</th>
                    <th class="pb-2">Data/Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($server->logs as $log)
                    <tr class="border-b last:border-0">
                        <td class="py-2">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $log->type=='error' ? 'bg-red-100 text-red-800' : ($log->type=='warning' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $log->type }}
                            </span>
                        </td>
                        <td class="py-2">{{ $log->message }}</td>
                        <td class="py-2">{{ $log->logged_at ? $log->logged_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-center text-gray-400" colspan="3">Nenhum log encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.servers.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Servidores</a>
</div>
@endsection
