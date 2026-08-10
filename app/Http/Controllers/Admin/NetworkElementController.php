<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkElement;
use App\Services\NetworkMapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NetworkElementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = NetworkElement::with('parent');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('serial', 'like', "%{$s}%")
                    ->orWhere('identifier', 'like', "%{$s}%");
            });
        }

        $elements = $query->orderBy('type')->orderBy('name')->paginate(20);

        return view('admin.network_elements.index', compact('elements'));
    }

    public function create(): View
    {
        $parents = NetworkElement::whereIn('type', ['olt', 'splitter', 'cto'])
            ->orderBy('name')
            ->get();

        return view('admin.network_elements.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string|max:50',
            'model' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:network_elements,id',
            'order' => 'nullable|integer',
            'identifier' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'server_id' => 'nullable|exists:servers,id',
            'notes' => 'nullable|string',
        ]);

        NetworkElement::create($validated);

        return redirect()->route('admin.network-elements.index')
            ->with('success', 'Elemento de rede cadastrado com sucesso.');
    }

    public function show(NetworkElement $networkElement): View
    {
        $networkElement->load(['parent', 'children', 'server']);
        return view('admin.network_elements.show', compact('networkElement'));
    }

    public function edit(NetworkElement $networkElement): View
    {
        $parents = NetworkElement::whereIn('type', ['olt', 'splitter', 'cto'])
            ->where('id', '!=', $networkElement->id)
            ->orderBy('name')
            ->get();

        return view('admin.network_elements.edit', compact('networkElement', 'parents'));
    }

    public function update(Request $request, NetworkElement $networkElement): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'type' => 'required|string|max:50',
            'model' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:network_elements,id',
            'order' => 'nullable|integer',
            'identifier' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'server_id' => 'nullable|exists:servers,id',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $networkElement->update($validated);

        return redirect()->route('admin.network-elements.index')
            ->with('success', 'Elemento de rede atualizado com sucesso.');
    }

    public function destroy(NetworkElement $networkElement): RedirectResponse
    {
        if ($networkElement->children()->count() > 0) {
            return back()->withErrors('Elemento possui filhos e não pode ser excluído.');
        }

        $networkElement->delete();

        return redirect()->route('admin.network-elements.index')
            ->with('success', 'Elemento de rede removido com sucesso.');
    }

    public function map(NetworkMapService $service): View
    {
        $geoJson = $service->exportToGeoJson();
        return view('admin.network_elements.map', compact('geoJson'));
    }

    public function tree(NetworkMapService $service): View
    {
        $tree = $service->getTree();
        return view('admin.network_elements.tree', compact('tree'));
    }
}
