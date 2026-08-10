<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = Equipment::with(['currentAssignment.client', 'supplier']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('serial', 'like', "%{$s}%")
                    ->orWhere('patrimony', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('model', 'like', "%{$s}%")
                    ->orWhere('mac', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $equipment = $query->latest()->paginate(20);
        $types = Equipment::select('type')->distinct()->pluck('type');

        return view('admin.equipment.index', compact('equipment', 'types'));
    }

    public function create(): View
    {
        return view('admin.equipment.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'serial' => 'required|string|max:100|unique:equipment,serial',
            'patrimony' => 'nullable|string|max:100|unique:equipment,patrimony',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'type' => 'required|string|max:50',
            'mac' => 'nullable|string|max:17',
            'ip_address' => 'nullable|string|max:45',
            'firmware_version' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipamento cadastrado com sucesso.');
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load(['assignments.client', 'assignments.assignedBy', 'supplier']);
        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        return view('admin.equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'serial' => "required|string|max:100|unique:equipment,serial,{$equipment->id}",
            'patrimony' => "nullable|string|max:100|unique:equipment,patrimony,{$equipment->id}",
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'type' => 'required|string|max:50',
            'mac' => 'nullable|string|max:17',
            'ip_address' => 'nullable|string|max:45',
            'firmware_version' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipamento atualizado com sucesso.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        if ($equipment->assignments()->active()->exists()) {
            return back()->withErrors('Equipamento está emprestado e não pode ser excluído.');
        }

        $equipment->delete();

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipamento removido com sucesso.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $imported = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';');

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $data = array_combine($header, $row);
                Equipment::updateOrCreate(
                    ['serial' => $data['serial']],
                    $data
                );
                $imported++;
            }
            fclose($handle);
        }

        return redirect()->route('admin.equipment.index')
            ->with('success', "{$imported} equipamentos importados com sucesso.");
    }
}
