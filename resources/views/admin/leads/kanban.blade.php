@extends('layouts.admin')
@section('title', 'Leads - Kanban')
@section('page-title', 'Leads - Kanban')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.leads.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Voltar para Lista</a>
    <a href="{{ route('admin.leads.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Lead</a>
</div>

@php
$statuses = ['new' => 'Novo', 'contacted' => 'Contactado', 'proposal' => 'Proposta', 'negotiation' => 'Negociação', 'won' => 'Ganho', 'lost' => 'Perdido'];
$headerColors = ['new' => 'bg-blue-500', 'contacted' => 'bg-amber-500', 'proposal' => 'bg-indigo-500', 'negotiation' => 'bg-purple-500', 'won' => 'bg-emerald-500', 'lost' => 'bg-red-500'];
@endphp

<div class="flex gap-4 overflow-x-auto pb-4">
    @foreach($statuses as $key => $label)
    @php $col = $grouped[$key] ?? collect(); @endphp
    <div class="flex-1 min-w-[260px] bg-gray-50 rounded-lg shadow-sm">
        <div class="{{ $headerColors[$key] }} text-white px-4 py-2 rounded-t-lg font-semibold text-sm flex items-center justify-between">
            <span>{{ $label }}</span>
            <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ $col->count() }}</span>
        </div>
        <div class="p-3 space-y-3 min-h-[200px]">
            @forelse($col as $lead)
            <div class="bg-white rounded border border-gray-200 p-3 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-1">
                    <a href="{{ route('admin.leads.show', $lead) }}" class="font-semibold text-sm text-primary-600 hover:underline">{{ $lead->name }}</a>
                </div>
                <p class="text-xs text-gray-500 truncate">{{ $lead->source ?? '-' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">{{ $lead->interest_plan ?? 'Sem plano' }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $lead->assignedTo?->name ?? 'Não atribuído' }}</p>
                @if($key === 'won' && $lead->convertedClient)
                <span class="inline-block mt-1 px-1.5 py-0.5 text-xs rounded bg-emerald-100 text-emerald-700">Cliente convertido</span>
                @endif
                <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="mt-2">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $lead->name }}">
                    <input type="hidden" name="email" value="{{ $lead->email }}">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded border-gray-300 border px-2 py-1">
                        @foreach($statuses as $sk => $sl)
                        <option value="{{ $sk }}" {{ $sk===$key ? 'selected' : '' }}>{{ $sl }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">Nenhum lead</p>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
