<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = EquipmentAssignment::with(['equipment', 'client', 'assignedBy']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('returned_at');
            } else {
                $query->whereNotNull('returned_at');
            }
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $assignments = $query->latest('assigned_at')->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        $equipment = Equipment::available()->get();
        $clients = Client::orderBy('name_display')->get();

        return view('admin.assignments.create', compact('equipment', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'client_id' => 'required|exists:clients,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'condition_out' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return back()->withErrors('Equipamento não está disponível.');
        }

        $validated['assigned_by'] = auth()->id();
        $validated['assigned_at'] = now();

        EquipmentAssignment::create($validated);

        $equipment->update(['status' => 'emprestado']);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Equipamento vinculado com sucesso.');
    }

    public function show(EquipmentAssignment $assignment): View
    {
        $assignment->load(['equipment', 'client', 'contract', 'assignedBy']);
        return view('admin.assignments.show', compact('assignment'));
    }

    public function returnEquipment(Request $request, EquipmentAssignment $assignment): RedirectResponse
    {
        if (!$assignment->isActive()) {
            return back()->withErrors('Este equipamento já foi devolvido.');
        }

        $validated = $request->validate([
            'condition_in' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $assignment->update([
            'returned_at' => now(),
            'condition_in' => $validated['condition_in'] ?? null,
            'notes' => $validated['notes'] ?? $assignment->notes,
        ]);

        $assignment->equipment->update(['status' => 'disponivel']);

        return redirect()->route('admin.assignments.show', $assignment)
            ->with('success', 'Equipamento devolvido com sucesso.');
    }
}
