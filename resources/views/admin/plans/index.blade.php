@extends('layouts.admin')

@section('title', 'Planos')
@section('page-title', 'Planos')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <p class="text-sm text-gray-600">Total: {{ $plans->total() }} planos</p>
        @if(auth()->user()->hasRole(['developer', 'admin', 'administrativo']))
        <a href="{{ route('admin.plans.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-primary-700">Novo Plano</a>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600">
                    <th class="p-3 font-medium">Nome</th>
                    <th class="p-3 font-medium">Velocidade</th>
                    <th class="p-3 font-medium">Tráfego</th>
                    <th class="p-3 font-medium">Preço</th>
                    <th class="p-3 font-medium">Ciclo</th>
                    <th class="p-3 font-medium">Ativo</th>
                    <th class="p-3 font-medium">Ordem</th>
                    <th class="p-3 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $plan->name }}</td>
                        <td class="p-3">{{ $plan->speed_label }}</td>
                        <td class="p-3">{{ $plan->traffic_type_label }}</td>
                        <td class="p-3">{{ $plan->price_formatted }}</td>
                        <td class="p-3">{{ $plan->billing_cycle_label }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $plan->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $plan->is_active ? 'Sim' : 'Não' }}
                            </span>
                        </td>
                        <td class="p-3">{{ $plan->order ?? '-' }}</td>
                        <td class="p-3 text-right">
                            @if(auth()->user()->hasRole(['developer', 'admin', 'administrativo']))
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-primary-600 hover:text-primary-800 mr-2">Editar</a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Excluir este plano?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs">Somente leitura</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-6 text-center text-gray-400" colspan="8">Nenhum plano encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t">
        @include('components.pagination', ['paginator' => $plans])
    </div>
</div>
@endsection
