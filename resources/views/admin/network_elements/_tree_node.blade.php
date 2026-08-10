<li>
    <div class="flex items-center gap-2 py-1.5 px-2 rounded hover:bg-gray-50 {{ $depth > 0 ? 'ml-' . min($depth * 6, 12) : '' }}" style="padding-left: {{ $depth * 24 }}px;">
        @php
            $icon = match($node->type) {
                'olt' => '🔴',
                'splitter' => '🟠',
                'cto' => '🔵',
                'client' => '🟢',
                'drop' => '🟡',
                'caixa' => '⬜',
                'armario' => '🟣',
                'backbone' => '🟤',
                default => '⚪'
            };
        @endphp
        <span class="text-xs">{{ $icon }}</span>
        <span class="text-xs text-gray-400 font-mono w-16">{{ $node->type_label }}</span>
        <a href="{{ route('admin.network-elements.show', $node) }}" class="text-primary-600 hover:underline font-medium">
            {{ $node->name }}
        </a>
        @if($node->identifier)
            <span class="text-xs text-gray-400 font-mono">({{ $node->identifier }})</span>
        @endif
        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $node->status=='active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
            {{ $node->status_label }}
        </span>
    </div>
    @if($node->childrenRecursive && $node->childrenRecursive->count() > 0)
        <ul class="space-y-1">
            @foreach($node->childrenRecursive as $child)
                @include('admin.network_elements._tree_node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
