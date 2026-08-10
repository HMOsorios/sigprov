@if ($paginator->hasPages())
    <nav class="flex items-center justify-between mt-4">
        <p class="text-sm text-gray-600">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} registros
        </p>
        <div class="flex gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border rounded hover:bg-gray-50">Anterior</a>
            @endif
            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="px-3 py-1.5 text-sm rounded {{ $page === $paginator->currentPage() ? 'bg-primary-600 text-white' : 'text-gray-700 bg-white border hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border rounded hover:bg-gray-50">Próximo</a>
            @else
                <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Próximo</span>
            @endif
        </div>
    </nav>
@endif
