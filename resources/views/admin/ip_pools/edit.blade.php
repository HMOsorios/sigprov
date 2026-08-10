@extends('layouts.admin')
@section('title', 'Editar Pool de IP')
@section('page-title', 'Editar Pool de IP')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.ip-pools.update', $ipPool) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $ipPool->name) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subnet *</label>
                <input type="text" name="subnet" value="{{ old('subnet', $ipPool->subnet) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('subnet') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Range Início *</label>
                <input type="text" name="range_start" value="{{ old('range_start', $ipPool->range_start) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('range_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Range Fim *</label>
                <input type="text" name="range_end" value="{{ old('range_end', $ipPool->range_end) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('range_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gateway *</label>
                <input type="text" name="gateway" value="{{ old('gateway', $ipPool->gateway) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('gateway') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="ipv4" {{ old('type', $ipPool->type)=='ipv4' ? 'selected' : '' }}>IPv4</option>
                    <option value="ipv6" {{ old('type', $ipPool->type)=='ipv6' ? 'selected' : '' }}>IPv6</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNS 1</label>
                <input type="text" name="dns1" value="{{ old('dns1', $ipPool->dns1) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('dns1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNS 2</label>
                <input type="text" name="dns2" value="{{ old('dns2', $ipPool->dns2) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('dns2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Servidor</label>
                <select name="server_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Server::orderBy('name')->get() as $server)
                    <option value="{{ $server->id }}" {{ old('server_id', $ipPool->server_id)==$server->id ? 'selected' : '' }}>{{ $server->name }}</option>
                    @endforeach
                </select>
                @error('server_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="active" {{ old('status', $ipPool->status)=='active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ old('status', $ipPool->status)=='inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_cgnat" value="1" {{ old('is_cgnat', $ipPool->is_cgnat) ? 'checked' : '' }}>
                    Pool CGNAT
                </label>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $ipPool->notes) }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Atualizar</button>
            <a href="{{ route('admin.ip-pools.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
