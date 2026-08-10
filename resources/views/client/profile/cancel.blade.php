@extends('layouts.client')
@section('title', 'Solicitar Cancelamento')
@section('content')
<div class="max-w-lg mx-auto bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Solicitar Cancelamento</h3>
    <p class="text-sm text-gray-500 mb-4">Contrato: <strong>{{ $contract->contract_number }}</strong></p>
    <p class="text-sm text-gray-500 mb-6">Preencha o motivo do cancelamento. Entraremos em contato para confirmar.</p>
    <form method="POST" action="{{ route('client.contracts.cancel-submit', $contract) }}">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo do Cancelamento</label>
            <textarea name="reason" rows="4" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Descreva o motivo..."></textarea>
            @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded text-sm hover:bg-red-700">Solicitar Cancelamento</button>
            <a href="{{ route('client.contracts') }}" class="text-gray-600 hover:text-gray-800 text-sm">Voltar</a>
        </div>
    </form>
</div>
@endsection
