@extends('layouts.admin')

@section('title', isset($contract) ? 'Editar Contrato' : 'Novo Contrato')
@section('page-title', isset($contract) ? 'Editar Contrato' : 'Novo Contrato')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($contract) ? route('admin.contracts.update', $contract) : route('admin.contracts.store') }}">
        @csrf
        @isset($contract) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select name="client_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id', $contract->client_id ?? '')==$c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plano *</label>
                <select name="plan_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->id }}" {{ old('plan_id', $contract->plan_id ?? '')==$p->id ? 'selected' : '' }}>{{ $p->name }} - {{ $p->price_formatted }}</option>
                    @endforeach
                </select>
                @error('plan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Início *</label>
                <input type="date" name="start_date" value="{{ old('start_date', isset($contract) ? $contract->start_date->format('Y-m-d') : '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Término</label>
                <input type="date" name="end_date" value="{{ old('end_date', isset($contract) && $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dia de Vencimento *</label>
                <input type="number" name="due_day" min="1" max="31" value="{{ old('due_day', $contract->due_day ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('due_day') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Contratado *</label>
                <input type="number" step="0.01" name="signed_price" value="{{ old('signed_price', $contract->signed_price ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('signed_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Desconto</label>
                <select name="discount_type" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="percent" {{ old('discount_type', $contract->discount_type ?? '')=='percent' ? 'selected' : '' }}>Percentual</option>
                    <option value="fixed" {{ old('discount_type', $contract->discount_type ?? '')=='fixed' ? 'selected' : '' }}>Fixo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desconto (%)</label>
                <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $contract->discount_percent ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('discount_percent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Desconto</label>
                <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $contract->discount_value ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>

            @isset($contract)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ $contract->status=='active' ? 'selected' : '' }}>Ativo</option>
                        <option value="suspended" {{ $contract->status=='suspended' ? 'selected' : '' }}>Suspenso</option>
                        <option value="canceled" {{ $contract->status=='canceled' ? 'selected' : '' }}>Cancelado</option>
                        <option value="expired" {{ $contract->status=='expired' ? 'selected' : '' }}>Expirado</option>
                    </select>
                </div>
            @endisset

            <div class="md:col-span-2">
                <hr class="my-2">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Endereço de Instalação</h4>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                <input type="text" name="installation_address" value="{{ old('installation_address', $contract->installation_address ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('installation_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                <input type="text" name="installation_zipcode" value="{{ old('installation_zipcode', $contract->installation_zipcode ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                <input type="text" name="installation_neighborhood" value="{{ old('installation_neighborhood', $contract->installation_neighborhood ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                <input type="text" name="installation_city" value="{{ old('installation_city', $contract->installation_city ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                <select name="installation_state" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    @php $states = ['AC'=>'Acre','AL'=>'Alagoas','AP'=>'Amapá','AM'=>'Amazonas','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo','GO'=>'Goiás','MA'=>'Maranhão','MT'=>'Mato Grosso','MS'=>'Mato Grosso do Sul','MG'=>'Minas Gerais','PA'=>'Pará','PB'=>'Paraíba','PR'=>'Paraná','PE'=>'Pernambuco','PI'=>'Piauí','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RS'=>'Rio Grande do Sul','RO'=>'Rondônia','RR'=>'Roraima','SC'=>'Santa Catarina','SP'=>'São Paulo','SE'=>'Sergipe','TO'=>'Tocantins']; @endphp
                    @foreach($states as $uf => $name)
                        <option value="{{ $uf }}" {{ old('installation_state', $contract->installation_state ?? '')==$uf ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input type="text" name="installation_complement" value="{{ old('installation_complement', $contract->installation_complement ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                <input type="text" name="installation_latitude" value="{{ old('installation_latitude', $contract->installation_latitude ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                <input type="text" name="installation_longitude" value="{{ old('installation_longitude', $contract->installation_longitude ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-2">
                <hr class="my-2">
                <h4 class="text-md font-semibold text-gray-700 mb-4">Fidelidade / Carência</h4>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Carência (meses)</label>
                <input type="number" name="minimum_duration_months" min="0" value="{{ old('minimum_duration_months', $contract->minimum_duration_months ?? 12) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fórmula Multa</label>
                <select name="cancellation_fine_formula" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="proportional_remaining" {{ old('cancellation_fine_formula', $contract->cancellation_fine_formula ?? '')=='proportional_remaining' ? 'selected' : '' }}>Proporcional ao restante</option>
                    <option value="anatel" {{ old('cancellation_fine_formula', $contract->cancellation_fine_formula ?? '')=='anatel' ? 'selected' : '' }}>ANATEL</option>
                    <option value="fixed_20" {{ old('cancellation_fine_formula', $contract->cancellation_fine_formula ?? '')=='fixed_20' ? 'selected' : '' }}>20% do valor mensal</option>
                    <option value="fixed_30" {{ old('cancellation_fine_formula', $contract->cancellation_fine_formula ?? '')=='fixed_30' ? 'selected' : '' }}>30% do valor mensal</option>
                    <option value="fixed_50" {{ old('cancellation_fine_formula', $contract->cancellation_fine_formula ?? '')=='fixed_50' ? 'selected' : '' }}>50% do valor mensal</option>
                    <option value="">Sem multa</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $contract->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($contract) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.contracts.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
