<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\TechInventory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = TechInventory::with(['technician', 'equipment', 'workOrder']);

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'checked_out') {
                $query->whereNull('checked_in_at');
            } else {
                $query->whereNotNull('checked_in_at');
            }
        }

        $inventories = $query->latest('checked_out_at')->paginate(20);
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician']))->get();

        return view('admin.tech_inventories.index', compact('inventories', 'technicians'));
    }

    public function create(): View
    {
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician']))->get();
        $equipment = Equipment::available()->get();

        return view('admin.tech_inventories.create', compact('technicians', 'equipment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'condition_out' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return back()->withErrors('Equipamento não está disponível.');
        }

        $validated['checked_out_at'] = now();
        TechInventory::create($validated);

        $equipment->update(['status' => 'emprestado']);

        return redirect()->route('admin.tech-inventories.index')
            ->with('success', 'Equipamento retirado com sucesso.');
    }

    public function checkin(Request $request, TechInventory $techInventory): RedirectResponse
    {
        if (!$techInventory->isCheckedOut()) {
            return back()->withErrors('Este equipamento já foi devolvido.');
        }

        $validated = $request->validate([
            'condition_in' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $techInventory->update([
            'checked_in_at' => now(),
            'condition_in' => $validated['condition_in'] ?? null,
            'notes' => $validated['notes'] ?? $techInventory->notes,
        ]);

        Equipment::where('id', $techInventory->equipment_id)
            ->where('status', 'emprestado')
            ->update(['status' => 'disponivel']);

        return redirect()->route('admin.tech-inventories.index')
            ->with('success', 'Equipamento devolvido com sucesso.');
    }
}
