<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin');
    }

    public function index(Request $request): View
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('legal_name', 'like', "%{$s}%")
                    ->orWhere('trade_name', 'like', "%{$s}%")
                    ->orWhere('cnpj', 'like', "%{$s}%")
                    ->orWhere('contact', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->latest()->paginate(20);
        $categories = Supplier::select('category')->distinct()->pluck('category');

        return view('admin.suppliers.index', compact('suppliers', 'categories'));
    }

    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cnpj' => 'required|string|max:18|unique:suppliers,cnpj',
            'legal_name' => 'required|string|max:200',
            'trade_name' => 'nullable|string|max:200',
            'contact' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:200',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'category' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'active';
        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['equipment', 'warehouseItems']);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'cnpj' => "required|string|max:18|unique:suppliers,cnpj,{$supplier->id}",
            'legal_name' => 'required|string|max:200',
            'trade_name' => 'nullable|string|max:200',
            'contact' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:200',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'category' => 'nullable|string|max:50',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Fornecedor removido com sucesso.');
    }
}
