@extends('layouts.admin')

@section('title', isset($ticket) ? 'Editar Chamado' : 'Novo Chamado')
@section('page-title', isset($ticket) ? 'Editar Chamado' : 'Novo Chamado')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ isset($ticket) ? route('admin.tickets.update', $ticket) : route('admin.tickets.store') }}">
        @csrf
        @isset($ticket) @method('PUT') @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select name="client_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Selecione...</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id', $ticket->client_id ?? '')==$c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrato</label>
                <select name="contract_id" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Selecione...</option>
                    @foreach($contracts as $c)
                        <option value="{{ $c->id }}" {{ old('contract_id', $ticket->contract_id ?? '')==$c->id ? 'selected' : '' }}>
                            {{ $c->contract_number }} - {{ $c->client->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('contract_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Atribuir para</label>
                <select name="assigned_to" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Selecione...</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('assigned_to', $ticket->assigned_to ?? '')==$u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade *</label>
                <select name="priority" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="low" {{ old('priority', $ticket->priority ?? '')=='low' ? 'selected' : '' }}>Baixa</option>
                    <option value="medium" {{ old('priority', $ticket->priority ?? '')=='medium' ? 'selected' : '' }}>Média</option>
                    <option value="high" {{ old('priority', $ticket->priority ?? '')=='high' ? 'selected' : '' }}>Alta</option>
                    <option value="critical" {{ old('priority', $ticket->priority ?? '')=='critical' ? 'selected' : '' }}>Crítica</option>
                </select>
                @error('priority') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                <select name="category" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="technical" {{ old('category', $ticket->category ?? '')=='technical' ? 'selected' : '' }}>Técnico</option>
                    <option value="billing" {{ old('category', $ticket->category ?? '')=='billing' ? 'selected' : '' }}>Financeiro</option>
                    <option value="commercial" {{ old('category', $ticket->category ?? '')=='commercial' ? 'selected' : '' }}>Comercial</option>
                    <option value="installation" {{ old('category', $ticket->category ?? '')=='installation' ? 'selected' : '' }}>Instalação</option>
                    <option value="complaint" {{ old('category', $ticket->category ?? '')=='complaint' ? 'selected' : '' }}>Reclamação</option>
                    <option value="other" {{ old('category', $ticket->category ?? '')=='other' ? 'selected' : '' }}>Outro</option>
                </select>
                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @isset($ticket)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="open" {{ $ticket->status=='open' ? 'selected' : '' }}>Aberto</option>
                        <option value="in_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="waiting_client" {{ $ticket->status=='waiting_client' ? 'selected' : '' }}>Aguardando Cliente</option>
                        <option value="resolved" {{ $ticket->status=='resolved' ? 'selected' : '' }}>Resolvido</option>
                        <option value="closed" {{ $ticket->status=='closed' ? 'selected' : '' }}>Fechado</option>
                    </select>
                </div>
            @endisset

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Assunto *</label>
                <input type="text" name="subject" value="{{ old('subject', $ticket->subject ?? '') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            @if(!isset($ticket))
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem *</label>
                    <textarea name="message" rows="5" class="w-full rounded border-gray-300 border px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" required>{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">{{ isset($ticket) ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.tickets.index') }}" class="text-gray-600 hover:text-gray-800 text-sm px-4 py-2">Cancelar</a>
        </div>
    </form>
</div>
@endsection
