@extends('layouts.admin')

@section('title', isset($server) ? 'Editar Servidor' : 'Novo Servidor')
@section('page-title', isset($server) ? 'Editar Servidor' : 'Novo Servidor')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($server) ? route('admin.servers.update', $server) : route('admin.servers.store') }}">
        @csrf
        @isset($server) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $server->name ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hostname *</label>
                <input type="text" name="hostname" value="{{ old('hostname', $server->hostname ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('hostname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço IP *</label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $server->ip_address ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('ip_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Porta *</label>
                <input type="number" name="port" value="{{ old('port', $server->port ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="router" {{ old('type', $server->type ?? '')=='router' ? 'selected' : '' }}>Roteador</option>
                    <option value="switch" {{ old('type', $server->type ?? '')=='switch' ? 'selected' : '' }}>Switch</option>
                    <option value="server" {{ old('type', $server->type ?? '')=='server' ? 'selected' : '' }}>Servidor</option>
                    <option value="firewall" {{ old('type', $server->type ?? '')=='firewall' ? 'selected' : '' }}>Firewall</option>
                    <option value="nas" {{ old('type', $server->type ?? '')=='nas' ? 'selected' : '' }}>NAS</option>
                    <option value="radius" {{ old('type', $server->type ?? '')=='radius' ? 'selected' : '' }}>Radius</option>
                    <option value="dhcp" {{ old('type', $server->type ?? '')=='dhcp' ? 'selected' : '' }}>DHCP</option>
                    <option value="dns" {{ old('type', $server->type ?? '')=='dns' ? 'selected' : '' }}>DNS</option>
                    <option value="other" {{ old('type', $server->type ?? '')=='other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" name="brand" value="{{ old('brand', $server->brand ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                <input type="text" name="model" value="{{ old('model', $server->model ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Versão Firmware</label>
                <input type="text" name="firmware_version" value="{{ old('firmware_version', $server->firmware_version ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                <input type="text" name="location" value="{{ old('location', $server->location ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                <input type="text" name="username" value="{{ old('username', $server->username ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <div class="relative">
                    <input type="password" name="password" id="serverPassword" value="" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="{{ isset($server) ? 'Deixe em branco para manter' : '' }}">
                    <button type="button" onclick="const p=document.getElementById('serverPassword');p.type=p.type==='password'?'text':'password';" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 text-sm">Mostrar</button>
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @isset($server)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="online" {{ $server->status=='online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $server->status=='offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ $server->status=='maintenance' ? 'selected' : '' }}>Manutenção</option>
                        <option value="error" {{ $server->status=='error' ? 'selected' : '' }}>Erro</option>
                    </select>
                </div>
            @endisset

            <div class="md:col-span-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_monitored" value="1" {{ old('is_monitored', $server->is_monitored ?? true) ? 'checked' : '' }}>
                    Monitorado
                </label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $server->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($server) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.servers.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
