@extends('layouts.admin')
@section('title', 'Editar Lead')
@section('page-title', 'Editar Lead - '.$lead->name)
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $lead->name) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $lead->email) }}" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                <input type="text" name="cellphone" value="{{ old('cellphone', $lead->cellphone) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plano de Interesse</label>
                <input type="text" name="interest_plan" value="{{ old('interest_plan', $lead->interest_plan) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Origem</label>
                <input type="text" name="source" value="{{ old('source', $lead->source) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="new" {{ old('status', $lead->status)=='new' ? 'selected' : '' }}>Novo</option>
                    <option value="contacted" {{ old('status', $lead->status)=='contacted' ? 'selected' : '' }}>Contactado</option>
                    <option value="proposal" {{ old('status', $lead->status)=='proposal' ? 'selected' : '' }}>Proposta</option>
                    <option value="negotiation" {{ old('status', $lead->status)=='negotiation' ? 'selected' : '' }}>Negociação</option>
                    <option value="won" {{ old('status', $lead->status)=='won' ? 'selected' : '' }}>Ganho</option>
                    <option value="lost" {{ old('status', $lead->status)=='lost' ? 'selected' : '' }}>Perdido</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                <select name="assigned_to" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                    <option value="">Selecione</option>
                    @foreach($staff as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to)==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
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
                <input type="text" name="state" value="{{ old('state', $lead->state) }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">{{ old('notes', $lead->notes) }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Atualizar</button>
            <a href="{{ route('admin.leads.index') }}" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</a>
        </div>
    </form>
</div>
@endsection
