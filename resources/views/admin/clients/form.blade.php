@extends('layouts.admin')

@section('title', isset($client) ? 'Editar Cliente' : 'Novo Cliente')
@section('page-title', isset($client) ? 'Editar Cliente' : 'Novo Cliente')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($client) ? route('admin.clients.update', $client) : route('admin.clients.store') }}">
        @csrf
        @isset($client) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social *</label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia</label>
                <input type="text" name="fantasy_name" value="{{ old('fantasy_name', $client->fantasy_name ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('fantasy_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ *</label>
                <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $client->cpf_cnpj ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('cpf_cnpj') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RG/IE</label>
                <input type="text" name="rg_ie" value="{{ old('rg_ie', $client->rg_ie ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('rg_ie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Pessoa *</label>
                <select name="person_type" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="pf" {{ old('person_type', $client->person_type ?? '')=='pf' ? 'selected' : '' }}>Pessoa Física</option>
                    <option value="pj" {{ old('person_type', $client->person_type ?? '')=='pj' ? 'selected' : '' }}>Pessoa Jurídica</option>
                </select>
                @error('person_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $client->email ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                <input type="text" name="cellphone" value="{{ old('cellphone', $client->cellphone ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('cellphone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                <input type="text" name="zipcode" value="{{ old('zipcode', $client->zipcode ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('zipcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                <input type="text" name="address" value="{{ old('address', $client->address ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                <input type="text" name="address_number" value="{{ old('address_number', $client->address_number ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('address_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input type="text" name="complement" value="{{ old('complement', $client->complement ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('complement') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                <input type="text" name="neighborhood" value="{{ old('neighborhood', $client->neighborhood ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('neighborhood') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                <input type="text" name="city" value="{{ old('city', $client->city ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                <select name="state" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    @php $states = ['AC'=>'Acre','AL'=>'Alagoas','AP'=>'Amapá','AM'=>'Amazonas','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo','GO'=>'Goiás','MA'=>'Maranhão','MT'=>'Mato Grosso','MS'=>'Mato Grosso do Sul','MG'=>'Minas Gerais','PA'=>'Pará','PB'=>'Paraíba','PR'=>'Paraná','PE'=>'Pernambuco','PI'=>'Piauí','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RS'=>'Rio Grande do Sul','RO'=>'Rondônia','RR'=>'Roraima','SC'=>'Santa Catarina','SP'=>'São Paulo','SE'=>'Sergipe','TO'=>'Tocantins']; @endphp
                    @foreach($states as $uf => $name)
                        <option value="{{ $uf }}" {{ old('state', $client->state ?? '')==$uf ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <hr class="my-6">

        <h4 class="text-md font-semibold text-gray-700 mb-4">Contato</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Contato</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $client->contact_name ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone do Contato</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $client->contact_phone ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email do Contato</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $client->contact_email ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        @isset($client)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="active" {{ $client->status=='active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ $client->status=='inactive' ? 'selected' : '' }}>Inativo</option>
                    <option value="blocked" {{ $client->status=='blocked' ? 'selected' : '' }}>Bloqueado</option>
                    <option value="canceled" {{ $client->status=='canceled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
        @endisset

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
            <textarea name="observations" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('observations', $client->observations ?? '') }}</textarea>
        </div>

        @isset($client)
        <div class="mt-6 border-t pt-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Preferências de Notificação</h4>
            <div class="flex flex-wrap gap-6">
                @php $prefs = old('notification_preferences', $client->notification_preferences ?? []); @endphp
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="notification_preferences[mail]" value="0">
                    <input type="checkbox" name="notification_preferences[mail]" value="1" {{ ($prefs['mail'] ?? true) ? 'checked' : '' }}>
                    E-mail
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="notification_preferences[whatsapp]" value="0">
                    <input type="checkbox" name="notification_preferences[whatsapp]" value="1" {{ ($prefs['whatsapp'] ?? true) ? 'checked' : '' }}>
                    WhatsApp
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="notification_preferences[sms]" value="0">
                    <input type="checkbox" name="notification_preferences[sms]" value="1" {{ ($prefs['sms'] ?? false) ? 'checked' : '' }}>
                    SMS
                </label>
            </div>
        </div>
        @endisset

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">
                {{ isset($client) ? 'Atualizar' : 'Salvar' }}
            </button>
            <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
