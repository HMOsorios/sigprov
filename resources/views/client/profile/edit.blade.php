@extends('layouts.client')
@section('title', 'Meu Perfil')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados Cadastrais</h3>
        <form method="POST" action="{{ route('client.profile.update') }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                    <input type="text" name="cellphone" value="{{ old('cellphone', $client->cellphone) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" name="zipcode" value="{{ old('zipcode', $client->zipcode) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="address_number" value="{{ old('address_number', $client->address_number) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                    <input type="text" name="address" value="{{ old('address', $client->address) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="complement" value="{{ old('complement', $client->complement) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="neighborhood" value="{{ old('neighborhood', $client->neighborhood) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="city" value="{{ old('city', $client->city) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <input type="text" name="state" value="{{ old('state', $client->state) }}" maxlength="2" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Salvar</button>
            </div>
        </form>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Alterar Senha</h3>
            <form method="POST" action="{{ route('client.profile.password') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                        <input type="password" name="current_password" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <input type="password" name="password" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-900">Alterar Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
