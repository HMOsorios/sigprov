@extends('layouts.admin')
@section('title', 'Agendamento de OS')
@section('page-title', 'Sugestões de Agendamento')
@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-5 border-b">
        <p class="text-sm text-gray-600">Sugestões de agendamento baseadas na região e disponibilidade dos técnicos.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">OS #</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Cliente</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Tipo</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Região</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Técnico Sugerido</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Horário Sugerido</th>
                    <th class="px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($suggestions as $suggestion)
                <tr class="hover:bg-gray-50">
                    <form method="POST" action="{{ route('admin.work-orders.apply-schedule') }}">
                        @csrf
                        <input type="hidden" name="work_order_id" value="{{ $suggestion->work_order_id }}">
                        <td class="px-4 py-3 font-semibold">{{ $suggestion->work_order_id }}</td>
                        <td class="px-4 py-3">{{ $suggestion->client_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $suggestion->type_label ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $suggestion->region ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <select name="technician_id" class="rounded border-gray-300 border px-3 py-2 text-sm w-full">
                                @foreach($technicians as $t)
                                <option value="{{ $t->id }}" {{ $t->id == $suggestion->suggested_technician_id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="datetime-local" name="scheduled_at" value="{{ $suggestion->suggested_time ? \Carbon\Carbon::parse($suggestion->suggested_time)->format('Y-m-d\TH:i') : '' }}" class="rounded border-gray-300 border px-3 py-2 text-sm w-full">
                        </td>
                        <td class="px-4 py-3">
                            <button type="submit" class="bg-primary-600 text-white px-3 py-1.5 rounded text-xs hover:bg-primary-700">Aplicar</button>
                        </td>
                    </form>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhuma sugestão de agendamento disponível.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.work-orders.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para OS</a>
</div>
@endsection
