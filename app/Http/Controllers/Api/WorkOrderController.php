<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = WorkOrder::with(['client', 'contract', 'technician']);

        if ($request->user()->role?->name === 'tech') {
            $query->where('technician_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        return response()->json([
            'data' => WorkOrderResource::collection($orders),
            'meta' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $orders = WorkOrder::with(['client', 'contract'])
            ->where('technician_id', $request->user()->id)
            ->whereDate('scheduled_at', now()->toDateString())
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'data' => WorkOrderResource::collection($orders),
        ]);
    }

    public function show(WorkOrder $workOrder): JsonResponse
    {
        $workOrder->load(['client', 'contract.plan', 'technician']);
        return response()->json(new WorkOrderResource($workOrder));
    }

    public function start(Request $request, WorkOrder $workOrder): JsonResponse
    {
        if ($workOrder->status !== 'scheduled' && $workOrder->status !== 'pending') {
            return response()->json(['message' => 'OS não pode ser iniciada.'], 422);
        }

        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json(new WorkOrderResource($workOrder->fresh()->load(['client', 'contract', 'technician'])));
    }

    public function complete(Request $request, WorkOrder $workOrder): JsonResponse
    {
        if ($workOrder->status !== 'in_progress') {
            return response()->json(['message' => 'OS não está em andamento.'], 422);
        }

        $validated = $request->validate([
            'resolution' => 'nullable|string',
        ]);

        $workOrder->update([
            'status' => 'completed',
            'finished_at' => now(),
            'resolution' => $validated['resolution'] ?? null,
        ]);

        return response()->json(new WorkOrderResource($workOrder->fresh()->load(['client', 'contract', 'technician'])));
    }

    public function uploadPhoto(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|max:10240',
        ]);

        $path = $request->file('photo')->store("work-orders/{$workOrder->id}", 'public');

        $photos = $workOrder->photos ?? [];
        $photos[] = $path;
        $workOrder->update(['photos' => $photos]);

        return response()->json(['path' => $path]);
    }

    public function uploadSignature(Request $request, WorkOrder $workOrder): JsonResponse
    {
        $request->validate([
            'signature' => 'required|image|max:5120',
        ]);

        $path = $request->file('signature')->store("work-orders/{$workOrder->id}", 'public');
        $workOrder->update(['client_signature' => $path]);

        return response()->json(['path' => $path]);
    }
}
