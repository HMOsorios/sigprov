@extends('layouts.admin')

@section('title', isset($invoice) ? 'Editar Fatura' : 'Nova Fatura')
@section('page-title', isset($invoice) ? 'Editar Fatura' : 'Nova Fatura')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($invoice) ? route('admin.invoices.update', $invoice) : route('admin.invoices.store') }}">
        @csrf
        @isset($invoice) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato *</label>
                <select name="contract_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach($contracts as $c)
                        <option value="{{ $c->id }}" {{ old('contract_id', $invoice->contract_id ?? '')==$c->id ? 'selected' : '' }}>
                            {{ $c->contract_number }} - {{ $c->client->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('contract_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Emissão *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', isset($invoice) ? $invoice->issue_date->format('Y-m-d') : '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('issue_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                <input type="date" name="due_date" value="{{ old('due_date', isset($invoice) ? $invoice->due_date->format('Y-m-d') : '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount', $invoice->amount ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desconto</label>
                <input type="number" step="0.01" name="discount" value="{{ old('discount', $invoice->discount ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                @error('discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @isset($invoice)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Multa</label>
                    <input type="number" step="0.01" name="late_fee" value="{{ old('late_fee', $invoice->late_fee ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Juros</label>
                    <input type="number" step="0.01" name="interest" value="{{ old('interest', $invoice->interest ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="pending" {{ $invoice->status=='pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="overdue" {{ $invoice->status=='overdue' ? 'selected' : '' }}>Vencida</option>
                        <option value="paid" {{ $invoice->status=='paid' ? 'selected' : '' }}>Paga</option>
                        <option value="canceled" {{ $invoice->status=='canceled' ? 'selected' : '' }}>Cancelada</option>
                        <option value="refunded" {{ $invoice->status=='refunded' ? 'selected' : '' }}>Estornada</option>
                    </select>
                </div>
            @endisset

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="3" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $invoice->notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($invoice) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.invoices.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
