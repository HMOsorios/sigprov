@extends('layouts.guest')
@section('title', 'Login')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Acessar Sistema</h2>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition @error('email') border-red-500 @enderror"
                placeholder="seu@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition"
                placeholder="Sua senha">
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                Lembrar-me
            </label>
            <a href="#" class="text-sm text-primary-600 hover:text-primary-800">Esqueceu a senha?</a>
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm">
            Entrar
        </button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-500">
        Ainda não tem conta?
        <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-800 font-medium">Cadastre-se</a>
    </p>
    <p class="mt-3 text-center text-sm">
        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-700">&larr; Voltar para a página inicial</a>
    </p>
</div>
@endsection
