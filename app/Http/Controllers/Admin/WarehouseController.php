<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarehouseItem;
use App\Models\WarehouseMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin');
    }

    public function index(Request $request): View
    {
        $query = WarehouseItem::with('supplier');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->lowStock();
            } elseif ($request->stock === 'out') {
                $query->outOfStock();
            }
        }

        $items = $query->latest()->paginate(20);
        $categories = WarehouseItem::select('category')->distinct()->pluck('category');

        $stats = [
            'total_items' => WarehouseItem::count(),
            'total_value' => WarehouseItem::sum(\DB::raw('current_qty * unit_price')),
            'low_stock' => WarehouseItem::lowStock()->count(),
            'out_of_stock' => WarehouseItem::outOfStock()->count(),
        ];

        return view('admin.warehouse.index', compact('items', 'categories', 'stats'));
    }

    public function create(): View
    {
        return view('admin.warehouse.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:warehouse_items,sku',
            'name' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'current_qty' => 'required|integer|min:0',
            'location' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $item = WarehouseItem::create($validated);

        if ($validated['current_qty'] > 0) {
            WarehouseMovement::create([
                'item_id' => $item->id,
                'type' => 'in',
                'qty' => $validated['current_qty'],
                'unit_price' => $validated['unit_price'],
                'reference_type' => 'adjustment',
                'notes' => 'Estoque inicial',
            ]);
        }

        return redirect()->route('admin.warehouse.index')
            ->with('success', 'Item cadastrado com sucesso.');
    }

    public function show(WarehouseItem $warehouseItem): View
    {
        $warehouseItem->load(['supplier', 'movements.responsible']);
        return view('admin.warehouse.show', compact('warehouseItem'));
    }

    public function edit(WarehouseItem $warehouseItem): View
    {
        return view('admin.warehouse.edit', compact('warehouseItem'));
    }

    public function update(Request $request, WarehouseItem $warehouseItem): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => "required|string|max:50|unique:warehouse_items,sku,{$warehouseItem->id}",
            'name' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'current_qty' => 'required|integer|min:0',
            'location' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $oldQty = $warehouseItem->current_qty;
        $warehouseItem->update($validated);

        if ($oldQty !== (int) $validated['current_qty']) {
            $diff = (int) $validated['current_qty'] - $oldQty;
            WarehouseMovement::create([
                'item_id' => $warehouseItem->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'qty' => abs($diff),
                'unit_price' => $validated['unit_price'],
                'reference_type' => 'adjustment',
                'notes' => 'Ajuste manual de estoque',
            ]);
        }

        return redirect()->route('admin.warehouse.index')
            ->with('success', 'Item atualizado com sucesso.');
    }

    public function movement(Request $request, WarehouseItem $warehouseItem): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'reference_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validated['type'] === 'out' && $warehouseItem->current_qty < $validated['qty']) {
            return back()->withErrors('Quantidade insuficiente em estoque.');
        }

        WarehouseMovement::create([
            'item_id' => $warehouseItem->id,
            'type' => $validated['type'],
            'qty' => $validated['qty'],
            'unit_price' => $validated['unit_price'] ?? $warehouseItem->unit_price,
            'reference_type' => $validated['reference_type'] ?? 'adjustment',
            'responsible_id' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $warehouseItem->decrement('current_qty', $validated['qty'] * ($validated['type'] === 'out' ? 1 : -1));

        return redirect()->route('admin.warehouse.show', $warehouseItem)
            ->with('success', 'Movimentação registrada com sucesso.');
    }

    public function destroy(WarehouseItem $warehouseItem): RedirectResponse
    {
        $warehouseItem->movements()->delete();
        $warehouseItem->delete();

        return redirect()->route('admin.warehouse.index')
            ->with('success', 'Item removido com sucesso.');
    }
}
