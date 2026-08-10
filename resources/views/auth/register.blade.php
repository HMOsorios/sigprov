@extends('layouts.guest')
@section('title', 'Cadastro')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Criar Conta</h2>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
            <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 outline-none transition">
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm">
            Criar Conta
        </button>
    </form>
    <p class="mt-6 text-center text-sm text-gray-500">
        Já tem conta? <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-800 font-medium">Faça login</a>
    </p>
</div>
@endsection
