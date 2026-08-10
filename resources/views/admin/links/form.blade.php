@extends('layouts.admin')

@section('title', isset($link) ? 'Editar Link' : 'Novo Link')
@section('page-title', isset($link) ? 'Editar Link' : 'Novo Link')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($link) ? route('admin.links.update', $link) : route('admin.links.store') }}">
        @csrf
        @isset($link) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato *</label>
                <select name="contract_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach($contracts as $c)
                        <option value="{{ $c->id }}" {{ old('contract_id', $link->contract_id ?? '')==$c->id ? 'selected' : '' }}>
                            {{ $c->contract_number }} - {{ $c->client->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('contract_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Servidor</label>
                <select name="server_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Selecione...</option>
                    @foreach($servers as $s)
                        <option value="{{ $s->id }}" {{ old('server_id', $link->server_id ?? '')==$s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->ip_address }})</option>
                    @endforeach
                </select>
                @error('server_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuário PPPoE</label>
                <input type="text" name="pppoe_user" value="{{ old('pppoe_user', $link->pppoe_user ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('pppoe_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha PPPoE</label>
                <input type="text" name="pppoe_password" value="{{ old('pppoe_password', $link->pppoe_password ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('pppoe_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço IP</label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $link->ip_address ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('ip_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">MAC Address</label>
                <input type="text" name="mac_address" value="{{ old('mac_address', $link->mac_address ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('mac_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">VLAN</label>
                <input type="text" name="vlan" value="{{ old('vlan', $link->vlan ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('vlan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ONT Serial</label>
                <input type="text" name="ont_serial" value="{{ old('ont_serial', $link->ont_serial ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('ont_serial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ONT Marca</label>
                <input type="text" name="ont_brand" value="{{ old('ont_brand', $link->ont_brand ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ONT Modelo</label>
                <input type="text" name="ont_model" value="{{ old('ont_model', $link->ont_model ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Origem do Cabo</label>
                <input type="text" name="cable_origin" value="{{ old('cable_origin', $link->cable_origin ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Drop do Cabo</label>
                <input type="text" name="cable_drop" value="{{ old('cable_drop', $link->cable_drop ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localização do Splitter</label>
                <input type="text" name="splitter_location" value="{{ old('splitter_location', $link->splitter_location ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sinal RX (dBm)</label>
                <input type="number" name="signal_rx" value="{{ old('signal_rx', $link->signal_rx ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sinal TX (dBm)</label>
                <input type="number" name="signal_tx" value="{{ old('signal_tx', $link->signal_tx ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>

            @isset($link)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ $link->status=='active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ $link->status=='inactive' ? 'selected' : '' }}>Inativo</option>
                        <option value="blocked" {{ $link->status=='blocked' ? 'selected' : '' }}>Bloqueado</option>
                        <option value="maintenance" {{ $link->status=='maintenance' ? 'selected' : '' }}>Manutenção</option>
                    </select>
                </div>
            @endisset

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $link->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($link) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.links.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
