@extends('layouts.admin')
@section('title', 'Árvore de Rede')
@section('page-title', 'Árvore de Rede')
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    @if(count($tree) > 0)
        <ul class="space-y-1">
            @foreach($tree as $node)
                @include('admin.network_elements._tree_node', ['node' => $node, 'depth' => 0])
            @endforeach
        </ul>
    @else
        <p class="text-center text-gray-400 py-8">Nenhum elemento raiz encontrado</p>
    @endif
</div>

<div class="mt-6">
    <a href="{{ route('admin.network-elements.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">&larr; Voltar para Elementos de Rede</a>
</div>
@endsection
