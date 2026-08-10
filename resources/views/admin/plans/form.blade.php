@extends('layouts.admin')

@section('title', isset($plan) ? 'Editar Plano' : 'Novo Plano')
@section('page-title', isset($plan) ? 'Editar Plano' : 'Novo Plano')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($plan) ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
        @csrf
        @isset($plan) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('description', $plan->description ?? '') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Download *</label>
                <input type="number" step="0.01" name="download_speed" value="{{ old('download_speed', $plan->download_speed ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('download_speed') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload *</label>
                <input type="number" step="0.01" name="upload_speed" value="{{ old('upload_speed', $plan->upload_speed ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('upload_speed') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidade *</label>
                <select name="speed_unit" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="mbps" {{ old('speed_unit', $plan->speed_unit ?? '')=='mbps' ? 'selected' : '' }}>Mbps</option>
                    <option value="gbps" {{ old('speed_unit', $plan->speed_unit ?? '')=='gbps' ? 'selected' : '' }}>Gbps</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tráfego Mensal (GB)</label>
                <input type="number" name="monthly_traffic" value="{{ old('monthly_traffic', $plan->monthly_traffic ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Tráfego *</label>
                <select name="traffic_type" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="unlimited" {{ old('traffic_type', $plan->traffic_type ?? '')=='unlimited' ? 'selected' : '' }}>Ilimitado</option>
                    <option value="limited" {{ old('traffic_type', $plan->traffic_type ?? '')=='limited' ? 'selected' : '' }}>Limitado</option>
                    <option value="fup" {{ old('traffic_type', $plan->traffic_type ?? '')=='fup' ? 'selected' : '' }}>FUP</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Taxa de Instalação</label>
                <input type="number" step="0.01" name="setup_fee" value="{{ old('setup_fee', $plan->setup_fee ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duração do Contrato (meses) *</label>
                <input type="number" name="contract_duration" value="{{ old('contract_duration', $plan->contract_duration ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo de Cobrança *</label>
                <select name="billing_cycle" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle ?? '')=='monthly' ? 'selected' : '' }}>Mensal</option>
                    <option value="quarterly" {{ old('billing_cycle', $plan->billing_cycle ?? '')=='quarterly' ? 'selected' : '' }}>Trimestral</option>
                    <option value="semiannual" {{ old('billing_cycle', $plan->billing_cycle ?? '')=='semiannual' ? 'selected' : '' }}>Semestral</option>
                    <option value="annual" {{ old('billing_cycle', $plan->billing_cycle ?? '')=='annual' ? 'selected' : '' }}>Anual</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Máx. Conexões *</label>
                <input type="number" name="max_connections" value="{{ old('max_connections', $plan->max_connections ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tecnologia</label>
                <input type="text" name="technology" value="{{ old('technology', $plan->technology ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                <input type="number" name="order" value="{{ old('order', $plan->order ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qtd. IPs Estáticos</label>
                <input type="number" name="static_ip_qty" value="{{ old('static_ip_qty', $plan->static_ip_qty ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex items-center gap-6 mt-2">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="has_static_ip" value="1" {{ old('has_static_ip', $plan->has_static_ip ?? false) ? 'checked' : '' }}>
                    IP Estático
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
                    Ativo
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan->is_featured ?? false) ? 'checked' : '' }}>
                    Destaque
                </label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Funcionalidades (JSON)</label>
                <textarea name="features" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('features', is_array($plan->features ?? null) ? json_encode($plan->features) : ($plan->features ?? '')) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($plan) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.plans.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
