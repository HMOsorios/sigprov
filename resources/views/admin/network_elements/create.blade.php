@extends('layouts.admin')
@section('title', 'Novo Elemento de Rede')
@section('page-title', 'Novo Elemento de Rede')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.network-elements.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Selecione</option>
                    <option value="olt" {{ old('type')=='olt' ? 'selected' : '' }}>OLT</option>
                    <option value="splitter" {{ old('type')=='splitter' ? 'selected' : '' }}>Splitter</option>
                    <option value="cto" {{ old('type')=='cto' ? 'selected' : '' }}>CTO</option>
                    <option value="drop" {{ old('type')=='drop' ? 'selected' : '' }}>Drop</option>
                    <option value="client" {{ old('type')=='client' ? 'selected' : '' }}>Cliente</option>
                    <option value="caixa" {{ old('type')=='caixa' ? 'selected' : '' }}>Caixa de Passagem</option>
                    <option value="armario" {{ old('type')=='armario' ? 'selected' : '' }}>Armário</option>
                    <option value="backbone" {{ old('type')=='backbone' ? 'selected' : '' }}>Backbone</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                <input type="text" name="model" value="{{ old('model') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('model') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial</label>
                <input type="text" name="serial" value="{{ old('serial') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('serial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Identificador</label>
                <input type="text" name="identifier" value="{{ old('identifier') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('identifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                <input type="number" name="order" value="{{ old('order') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Elemento Pai</label>
                <select name="parent_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Nenhum</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id')==$p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->type_label }})</option>
                    @endforeach
                </select>
                @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Servidor</label>
                <select name="server_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Nenhum</option>
                    @foreach(\App\Models\Server::orderBy('name')->get() as $server)
                    <option value="{{ $server->id }}" {{ old('server_id')==$server->id ? 'selected' : '' }}>{{ $server->name }}</option>
                    @endforeach
                </select>
                @error('server_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                <input type="text" name="latitude" value="{{ old('latitude') }}" step="any" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                <input type="text" name="longitude" value="{{ old('longitude') }}" step="any" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                <input type="text" name="state" value="{{ old('state') }}" maxlength="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            <a href="{{ route('admin.network-elements.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
