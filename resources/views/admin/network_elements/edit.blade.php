@extends('layouts.admin')
@section('title', 'Editar Elemento de Rede')
@section('page-title', 'Editar Elemento de Rede')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.network-elements.update', $networkElement) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $networkElement->name) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Selecione</option>
                    <option value="olt" {{ old('type', $networkElement->type)=='olt' ? 'selected' : '' }}>OLT</option>
                    <option value="splitter" {{ old('type', $networkElement->type)=='splitter' ? 'selected' : '' }}>Splitter</option>
                    <option value="cto" {{ old('type', $networkElement->type)=='cto' ? 'selected' : '' }}>CTO</option>
                    <option value="drop" {{ old('type', $networkElement->type)=='drop' ? 'selected' : '' }}>Drop</option>
                    <option value="client" {{ old('type', $networkElement->type)=='client' ? 'selected' : '' }}>Cliente</option>
                    <option value="caixa" {{ old('type', $networkElement->type)=='caixa' ? 'selected' : '' }}>Caixa de Passagem</option>
                    <option value="armario" {{ old('type', $networkElement->type)=='armario' ? 'selected' : '' }}>Armário</option>
                    <option value="backbone" {{ old('type', $networkElement->type)=='backbone' ? 'selected' : '' }}>Backbone</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                <input type="text" name="model" value="{{ old('model', $networkElement->model) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('model') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial</label>
                <input type="text" name="serial" value="{{ old('serial', $networkElement->serial) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('serial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Identificador</label>
                <input type="text" name="identifier" value="{{ old('identifier', $networkElement->identifier) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('identifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                <input type="number" name="order" value="{{ old('order', $networkElement->order) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Elemento Pai</label>
                <select name="parent_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Nenhum</option>
                    @foreach($parents as $p)
                        @if($p->id != $networkElement->id)
                        <option value="{{ $p->id }}" {{ old('parent_id', $networkElement->parent_id)==$p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->type_label }})</option>
                        @endif
                    @endforeach
                </select>
                @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Servidor</label>
                <select name="server_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Server::orderBy('name')->get() as $server)
                    <option value="{{ $server->id }}" {{ old('server_id', $networkElement->server_id)==$server->id ? 'selected' : '' }}>{{ $server->name }}</option>
                    @endforeach
                </select>
                @error('server_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                <input type="text" name="latitude" value="{{ old('latitude', $networkElement->latitude) }}" step="any" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                <input type="text" name="longitude" value="{{ old('longitude', $networkElement->longitude) }}" step="any" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="city" value="{{ old('city', $networkElement->city) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                <input type="text" name="state" value="{{ old('state', $networkElement->state) }}" maxlength="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="active" {{ old('status', $networkElement->status)=='active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ old('status', $networkElement->status)=='inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <input type="text" name="address" value="{{ old('address', $networkElement->address) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $networkElement->notes) }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Atualizar</button>
            <a href="{{ route('admin.network-elements.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
