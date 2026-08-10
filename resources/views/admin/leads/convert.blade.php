@extends('layouts.admin')
@section('title', 'Converter Lead em Cliente')
@section('page-title', 'Converter Lead - '.$lead->name)
@section('content')
<div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded mb-6">
    <p class="text-sm text-amber-800">
        <strong>Convertendo lead:</strong> {{ $lead->name }} | {{ $lead->email }} | {{ $lead->phone ?? $lead->cellphone ?? 'Sem telefone' }} | Origem: {{ $lead->source ?? '-' }}
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ route('admin.leads.convert.store', $lead) }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Pessoa *</label>
                <div class="flex gap-4 mt-1">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="person_type" value="pf" {{ old('person_type', 'pf')=='pf' ? 'checked' : '' }} onchange="togglePjFields()"> Pessoa Física
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="person_type" value="pj" {{ old('person_type')=='pj' ? 'checked' : '' }} onchange="togglePjFields()"> Pessoa Jurídica
                    </label>
                </div>
            </div>
            <div></div>
            <div id="pj_company_name" style="{{ old('person_type')=='pj' ? '' : 'display:none' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social *</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div id="pj_fantasy_name" style="{{ old('person_type')=='pj' ? '' : 'display:none' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia</label>
                <input type="text" name="fantasy_name" value="{{ old('fantasy_name') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ *</label>
                <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('cpf_cnpj') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $lead->email) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                <input type="text" name="cellphone" value="{{ old('cellphone', $lead->cellphone) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input type="text" name="zipcode" value="{{ old('zipcode') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <input type="text" name="address" value="{{ old('address', $lead->address) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="city" value="{{ old('city', $lead->city) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="state" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @php $states = ['AC'=>'Acre','AL'=>'Alagoas','AP'=>'Amapá','AM'=>'Amazonas','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo','GO'=>'Goiás','MA'=>'Maranhão','MT'=>'Mato Grosso','MS'=>'Mato Grosso do Sul','MG'=>'Minas Gerais','PA'=>'Pará','PB'=>'Paraíba','PR'=>'Paraná','PE'=>'Pernambuco','PI'=>'Piauí','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RS'=>'Rio Grande do Sul','RO'=>'Rondônia','RR'=>'Roraima','SC'=>'Santa Catarina','SP'=>'São Paulo','SE'=>'Sergipe','TO'=>'Tocantins']; @endphp
                    @foreach($states as $uf => $name)
                    <option value="{{ $uf }}" {{ old('state', $lead->state)==$uf ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded text-sm hover:bg-emerald-700">Converter e Criar Cliente</button>
            <a href="{{ route('admin.leads.show', $lead) }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePjFields() {
    const isPj = document.querySelector('input[name="person_type"]:checked')?.value === 'pj';
    document.getElementById('pj_company_name').style.display = isPj ? '' : 'none';
    document.getElementById('pj_fantasy_name').style.display = isPj ? '' : 'none';
}
</script>
@endpush
@endsection
