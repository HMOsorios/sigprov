<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\TechInventory;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TechInventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = TechInventory::with(['equipment', 'workOrder'])
            ->where('technician_id', $request->user()->id)
            ->whereNull('checked_in_at')
            ->latest('checked_out_at')
            ->get();

        return response()->json([
            'data' => $items->map(fn($i) => [
                'id' => $i->id,
                'equipment_id' => $i->equipment_id,
                'serial' => $i->equipment->serial,
                'brand' => $i->equipment->brand,
                'model' => $i->equipment->model,
                'type' => $i->equipment->type_label,
                'checked_out_at' => $i->checked_out_at->toIso8601String(),
                'work_order_id' => $i->work_order_id,
            ]),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'condition_out' => 'nullable|string|max:50',
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailable()) {
            return response()->json(['error' => 'Equipamento não disponível'], 422);
        }

        $inventory = TechInventory::create([
            'technician_id' => $request->user()->id,
            'equipment_id' => $validated['equipment_id'],
            'work_order_id' => $validated['work_order_id'] ?? null,
            'condition_out' => $validated['condition_out'] ?? null,
            'checked_out_at' => now(),
        ]);

        $equipment->update(['status' => 'emprestado']);

        return response()->json([
            'data' => $inventory->load('equipment'),
            'message' => 'Equipamento retirado com sucesso.',
        ], 201);
    }

    public function checkin(Request $request, TechInventory $techInventory): JsonResponse
    {
        if (!$techInventory->isCheckedOut()) {
            return response()->json(['error' => 'Equipamento já devolvido'], 422);
        }

        if ($techInventory->technician_id !== $request->user()->id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'condition_in' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $techInventory->update([
            'checked_in_at' => now(),
            'condition_in' => $validated['condition_in'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        Equipment::where('id', $techInventory->equipment_id)
            ->where('status', 'emprestado')
            ->update(['status' => 'disponivel']);

        return response()->json([
            'message' => 'Equipamento devolvido com sucesso.',
            'data' => $techInventory->fresh()->load('equipment'),
        ]);
    }

    public function lookup(string $serial): JsonResponse
    {
        $equipment = Equipment::with(['currentAssignment.client', 'supplier'])
            ->where('serial', $serial)
            ->first();

        if (!$equipment) {
            return response()->json(['error' => 'Equipamento não encontrado'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $equipment->id,
                'serial' => $equipment->serial,
                'brand' => $equipment->brand,
                'model' => $equipment->model,
                'type' => $equipment->type_label,
                'status' => $equipment->status_label,
                'is_available' => $equipment->isAvailable(),
                'current_client' => $equipment->currentAssignment?->client?->name_display,
            ],
        ]);
    }

    public function assign(Request $request, string $serial): JsonResponse
    {
        $equipment = Equipment::where('serial', $serial)->first();

        if (!$equipment) {
            return response()->json(['error' => 'Equipamento não encontrado'], 404);
        }

        if (!$equipment->isAvailable()) {
            return response()->json(['error' => 'Equipamento não disponível'], 422);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'condition_out' => 'nullable|string|max:50',
        ]);

        EquipmentAssignment::create([
            'equipment_id' => $equipment->id,
            'client_id' => $validated['client_id'],
            'contract_id' => $validated['contract_id'] ?? null,
            'assigned_by' => $request->user()->id,
            'condition_out' => $validated['condition_out'] ?? null,
        ]);

        $equipment->update(['status' => 'emprestado']);

        return response()->json(['message' => 'Equipamento vinculado ao cliente com sucesso.'], 201);
    }
}
