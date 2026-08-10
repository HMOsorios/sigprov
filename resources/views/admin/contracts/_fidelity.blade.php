@php
    $monthsElapsed = $contract->start_date ? now()->diffInMonths($contract->start_date) : 0;
    $monthsRemaining = max(0, ($contract->minimum_duration_months ?? 0) - $monthsElapsed);
    $fidelityActive = $monthsRemaining > 0;
@endphp

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Fidelidade</h3>

    <dl class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-gray-500">Data de Início</dt>
            <dd class="font-medium">{{ $contract->start_date ? $contract->start_date->format('d/m/Y') : '-' }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Duração Mínima</dt>
            <dd class="font-medium">{{ $contract->minimum_duration_months ?? 0 }} meses</dd>
        </div>
        <div>
            <dt class="text-gray-500">Meses Transcorridos</dt>
            <dd class="font-medium">{{ $monthsElapsed }} meses</dd>
        </div>
        <div>
            <dt class="text-gray-500">Meses Restantes</dt>
            <dd class="font-medium">
                @if($fidelityActive)
                    <span class="text-amber-600">{{ $monthsRemaining }} meses</span>
                @else
                    <span class="text-emerald-600">Fidelidade cumprida</span>
                @endif
            </dd>
        </div>
    </dl>

    @if($fidelityActive && ($contract->cancellation_fine ?? 0) > 0)
    <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
        <p class="text-sm font-semibold text-red-800">Multa de Cancelamento</p>
        <p class="text-sm text-red-700">R$ {{ number_format($contract->cancellation_fine, 2, ',', '.') }}</p>
    </div>
    @endif

    <hr class="my-4">

    <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">Assinatura</span>
        @php
            $sigStatus = $contract->signature_status ?? 'pending';
            $sigLabel = match($sigStatus) { 'signed' => 'Assinado', 'sent' => 'Enviado', default => 'Pendente' };
            $sigColor = match($sigStatus) { 'signed' => 'bg-emerald-100 text-emerald-800', 'sent' => 'bg-blue-100 text-blue-800', default => 'bg-gray-100 text-gray-800' };
        @endphp
        <span class="px-2 py-0.5 text-xs rounded-full {{ $sigColor }}">{{ $sigLabel }}</span>
    </div>

    <div class="mt-3 flex flex-col gap-2">
        @if($sigStatus === 'pending')
        <form method="POST" action="{{ route('admin.contracts.send-signature', $contract) }}">
            @csrf
            <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Enviar para Assinatura</button>
        </form>
        @endif
        @if(in_array($sigStatus, ['sent', 'pending']))
        <form method="POST" action="{{ route('admin.contracts.mark-signed', $contract) }}" onsubmit="return confirm('Marcar como assinado?')">
            @csrf
            <button type="submit" class="w-full bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">Marcar como Assinado</button>
        </form>
        @endif
    </div>
</div>
