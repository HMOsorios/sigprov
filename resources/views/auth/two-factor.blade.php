@extends('layouts.guest')
@section('title', 'Autenticação em Duas Etapas')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Verificação em Duas Etapas</h2>
    <p class="text-center text-sm text-gray-500 mb-6">Digite o código do seu aplicativo autenticador</p>
    <form method="POST" action="{{ route('two-factor.challenge') }}" class="space-y-4">
        @csrf
        <div>
            <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus
                class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition"
                placeholder="000000">
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm">
            Verificar
        </button>
    </form>
    <hr class="my-6">
    <form method="POST" action="{{ route('two-factor.recovery') }}" class="space-y-3">
        @csrf
        <p class="text-sm text-gray-500 text-center">Use um código de recuperação</p>
        <input type="text" name="recovery_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition text-center" placeholder="Código de recuperação">
        <button type="submit" class="w-full text-sm text-gray-600 border border-gray-300 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg transition">
            Usar código de recuperação
        </button>
    </form>
</div>
@endsection
