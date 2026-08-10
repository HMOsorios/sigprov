@extends('layouts.admin')
@section('title', 'OS #'.$workOrder->id)
@section('page-title', 'OS #'.$workOrder->id.' - '.$workOrder->client?->name_display)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados da OS</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">ID</dt><dd class="font-medium font-mono">{{ $workOrder->id }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium">{{ $workOrder->type_label }}</dd></div>
            <div><dt class="text-gray-500">Prioridade</dt><dd>
                @php
                    $pc = match($workOrder->priority) {
                        'critical' => 'bg-red-100 text-red-800',
                        'high' => 'bg-orange-100 text-orange-800',
                        'medium' => 'bg-blue-100 text-blue-800',
                        'low' => 'bg-gray-100 text-gray-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="px-2 py-0.5 text-xs rounded-full {{ $pc }}">{{ $workOrder->priority_label }}</span>
            </dd></div>
            <div><dt class="text-gray-500">Status</dt><dd>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $workOrder->status_color=='success' ? 'bg-emerald-100 text-emerald-800' : ($workOrder->status_color=='warning' ? 'bg-amber-100 text-amber-800' : ($workOrder->status_color=='danger' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                    {{ $workOrder->status_label }}
                </span>
            </dd></div>
            <div><dt class="text-gray-500">Cliente</dt><dd><a href="{{ route('admin.clients.show', $workOrder->client) }}" class="text-primary-600 hover:underline">{{ $workOrder->client?->name_display ?? '-' }}</a></dd></div>
            <div><dt class="text-gray-500">Contrato</dt><dd>{{ $workOrder->contract?->id ? 'Contrato #'.$workOrder->contract->id : '-' }}</dd></div>
            <div><dt class="text-gray-500">Técnico</dt><dd>{{ $workOrder->technician?->name ?? '-' }}</dd></div>
            <div><dt class="text-gray-500">Agendado para</dt><dd>{{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('d/m/Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Iniciado em</dt><dd>{{ $workOrder->started_at ? $workOrder->started_at->format('d/m/Y H:i') : '-' }}</dd></div>
            <div><dt class="text-gray-500">Finalizado em</dt><dd>{{ $workOrder->finished_at ? $workOrder->finished_at->format('d/m/Y H:i') : '-' }}</dd></div>
            <div class="col-span-2"><dt class="text-gray-500">Descrição</dt><dd class="font-medium">{{ $workOrder->description ?? '-' }}</dd></div>
            @if($workOrder->resolution)
            <div class="col-span-2"><dt class="text-gray-500">Resolução</dt><dd class="font-medium">{{ $workOrder->resolution }}</dd></div>
            @endif
        </dl>

        @if($workOrder->photos && count($workOrder->photos) > 0)
        <hr class="my-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Fotos</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($workOrder->photos as $photo)
            <a href="{{ Storage::url($photo) }}" target="_blank" class="w-20 h-20 rounded border overflow-hidden bg-gray-100">
                <img src="{{ Storage::url($photo) }}" alt="Foto" class="w-full h-full object-cover">
            </a>
            @endforeach
        </div>
        @endif

        @if($workOrder->signature)
        <hr class="my-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Assinatura</h4>
        <a href="{{ Storage::url($workOrder->signature) }}" target="_blank" class="text-primary-600 hover:underline text-sm">Ver assinatura</a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações</h3>
        <div class="flex flex-col gap-2">
            @if(in_array($workOrder->status, ['pending', 'scheduled']))
            <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="w-full bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Atribuir Técnico</button>
            @endif

            @if(in_array($workOrder->status, ['scheduled', 'pending']))
            <form method="POST" action="{{ route('admin.work-orders.start', $workOrder) }}" onsubmit="return confirm('Iniciar esta OS?')">
                @csrf
                <button type="submit" class="w-full bg-amber-500 text-white px-4 py-2 rounded text-sm hover:bg-amber-600">Iniciar</button>
            </form>
            @endif

            @if($workOrder->status == 'in_progress')
            <form method="POST" action="{{ route('admin.work-orders.complete', $workOrder) }}" onsubmit="return confirm('Finalizar esta OS?')">
                @csrf
                <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Finalizar</button>
            </form>
            @endif

            @if(!in_array($workOrder->status, ['completed', 'canceled']))
            <form method="POST" action="{{ route('admin.work-orders.cancel', $workOrder) }}" onsubmit="return confirm('Cancelar esta OS?')">
                @csrf
                <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">Cancelar</button>
            </form>
            @endif

            <hr class="my-2">
            <a href="{{ route('admin.work-orders.edit', $workOrder) }}" class="w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200">Editar</a>
        </div>
    </div>
</div>

<div id="assignModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Atribuir Técnico</h3>
        <form method="POST" action="{{ route('admin.work-orders.assign', $workOrder) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Técnico *</label>
                    <select name="technician_id" required class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                        <option value="">Selecione</option>
                        @foreach($technicians as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data agendada</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ $workOrder->scheduled_at?->format('Y-m-d\TH:i') }}" class="w-full rounded border-gray-300 border px-3 py-2 text-sm">
                </div>
            </div>
            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-600 px-4 py-2 text-sm hover:text-gray-800">Cancelar</button>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded text-sm hover:bg-primary-700">Atribuir</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.work-orders.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para OS</a>
</div>
@endsection
