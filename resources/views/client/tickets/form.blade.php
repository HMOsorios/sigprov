@extends('layouts.client')

@section('title', 'Novo Chamado')

@section('content')
    <div class="mb-6">
        <a href="{{ route('client.tickets.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">&larr; Voltar para Chamados</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Novo Chamado</h1>
        <p class="text-gray-500 mt-1">Abra um chamado de suporte técnico.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('client.tickets.store') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select name="client_id" id="client_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('client_id') border-red-500 @enderror">
                        <option value="">Selecione um cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name_display }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contract_id" class="block text-sm font-medium text-gray-700 mb-1">Contrato <span class="text-gray-400">(opcional)</span></label>
                    <select name="contract_id" id="contract_id" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('contract_id') border-red-500 @enderror">
                        <option value="">Selecione um contrato</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" data-client="{{ $contract->client_id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>{{ $contract->contract_number }} - {{ $contract->plan?->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    @error('contract_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Assunto</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subject') border-red-500 @enderror" placeholder="Resumo do problema">
                    @error('subject') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
                        <select name="priority" id="priority" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('priority') border-red-500 @enderror">
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('priority') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select name="category" id="category" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('category') border-red-500 @enderror">
                            <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Técnico</option>
                            <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Financeiro</option>
                            <option value="commercial" {{ old('category') === 'commercial' ? 'selected' : '' }}>Comercial</option>
                            <option value="installation" {{ old('category') === 'installation' ? 'selected' : '' }}>Instalação</option>
                            <option value="complaint" {{ old('category') === 'complaint' ? 'selected' : '' }}>Reclamação</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('category') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                    <textarea name="message" id="message" rows="6" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('message') border-red-500 @enderror" placeholder="Descreva detalhadamente o seu problema...">{{ old('message') }}</textarea>
                    @error('message') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition shadow-sm">Enviar Chamado</button>
                <a href="{{ route('client.tickets.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clientSelect = document.getElementById('client_id');
            const contractSelect = document.getElementById('contract_id');
            const contracts = @json($contracts);
            function filterContracts() {
                const clientId = clientSelect.value;
                const options = contractSelect.querySelectorAll('option');
                options.forEach(opt => {
                    if (opt.value === '') return;
                    if (opt.dataset.client === clientId || !clientId) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });
                if (contractSelect.value && contractSelect.selectedOptions[0]?.style?.display === 'none') {
                    contractSelect.value = '';
                }
            }
            clientSelect.addEventListener('change', filterContracts);
            filterContracts();
        });
    </script>
    @endpush
@endsection