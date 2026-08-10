<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\SchedulingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin,technician');
    }

    public function index(Request $request): View
    {
        $query = WorkOrder::with(['client', 'technician']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                    ->orWhere('id', 'like', "%{$s}%")
                    ->orWhereHas('client', fn($c) => $c->where('name_display', 'like', "%{$s}%"));
            });
        }

        $viewMode = $request->view ?? 'list';

        if ($viewMode === 'kanban') {
            $orders = $query->orderBy('priority')->orderBy('created_at')->get();
            $grouped = $orders->groupBy('status');
            return view('admin.work_orders.kanban', compact('grouped'));
        }

        if ($viewMode === 'calendar') {
            $orders = $query->whereNotNull('scheduled_at')->orderBy('scheduled_at')->get();

            $month = (int) ($request->month ?? now()->month);
            $year = (int) ($request->year ?? now()->year);

            $date = \Carbon\Carbon::create($year, $month, 1);
            $monthName = $date->translatedFormat('F');
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            $startOfCalendar = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $endOfCalendar = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
            $weeks = [];

            $current = $startOfCalendar->copy();
            while ($current <= $endOfCalendar) {
                $week = [];
                for ($i = 0; $i < 7; $i++) {
                    $week[] = $current->copy();
                    $current->addDay();
                }
                $weeks[] = $week;
            }

            $prevMonth = $month == 1 ? 12 : $month - 1;
            $nextMonth = $month == 12 ? 1 : $month + 1;

            return view('admin.work_orders.calendar', compact('orders', 'prevMonth', 'nextMonth', 'monthName', 'year', 'weeks', 'month'));
        }

        $orders = $query->latest()->paginate(20);
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician']))->get();

        return view('admin.work_orders.index', compact('orders', 'technicians'));
    }

    public function create(): View
    {
        return view('admin.work_orders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'technician_id' => 'nullable|exists:users,id',
            'type' => 'required|in:install,maintenance,repair,remove,visit',
            'priority' => 'required|in:low,medium,high,critical',
            'scheduled_at' => 'nullable|date',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = $validated['scheduled_at'] ? 'scheduled' : 'pending';

        WorkOrder::create($validated);

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'Ordem de serviço criada com sucesso.');
    }

    public function show(WorkOrder $workOrder): View
    {
        $workOrder->load(['client', 'contract', 'technician']);
        return view('admin.work_orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder): View
    {
        return view('admin.work_orders.edit', compact('workOrder'));
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'technician_id' => 'nullable|exists:users,id',
            'type' => 'required|in:install,maintenance,repair,remove,visit',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:pending,scheduled,in_progress,completed,canceled',
            'scheduled_at' => 'nullable|date',
            'description' => 'required|string',
            'resolution' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $workOrder->update($validated);

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'OS atualizada com sucesso.');
    }

    public function assign(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:users,id',
            'scheduled_at' => 'nullable|date',
        ]);

        $data = ['technician_id' => $validated['technician_id']];

        if (!empty($validated['scheduled_at'])) {
            $data['scheduled_at'] = $validated['scheduled_at'];
            $data['status'] = 'scheduled';
        }

        $workOrder->update($data);

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'OS atribuída com sucesso.');
    }

    public function start(WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status !== 'scheduled' && $workOrder->status !== 'pending') {
            return back()->withErrors('OS não pode ser iniciada a partir do status atual.');
        }

        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'OS iniciada com sucesso.');
    }

    public function complete(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status !== 'in_progress') {
            return back()->withErrors('OS precisa estar em andamento para ser finalizada.');
        }

        $validated = $request->validate([
            'resolution' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $workOrder->update([
            'status' => 'completed',
            'finished_at' => now(),
            'resolution' => $validated['resolution'],
            'notes' => $validated['notes'] ?? $workOrder->notes,
        ]);

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'OS finalizada com sucesso.');
    }

    public function cancel(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        if (in_array($workOrder->status, ['completed', 'canceled'])) {
            return back()->withErrors('OS já está finalizada ou cancelada.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $workOrder->update([
            'status' => 'canceled',
            'notes' => $validated['notes'] ?? $workOrder->notes,
        ]);

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'OS cancelada com sucesso.');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        if ($workOrder->status === 'in_progress') {
            return back()->withErrors('OS em andamento não pode ser excluída.');
        }

        $workOrder->delete();

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'OS removida com sucesso.');
    }

    public function schedule(SchedulingService $service): View
    {
        $suggestions = $service->suggestSchedule();
        $technicians = User::whereHas('roles', fn($q) => $q->whereIn('name', ['technician']))->get();

        return view('admin.work_orders.schedule', compact('suggestions', 'technicians'));
    }

    public function applySchedule(Request $request, SchedulingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'technician_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
        ]);

        $service->applySuggestion(
            $validated['work_order_id'],
            $validated['technician_id'],
            $validated['scheduled_at']
        );

        return redirect()->route('admin.work-orders.schedule')
            ->with('success', 'Agendamento aplicado com sucesso.');
    }

    public function map(Request $request): View
    {
        $query = WorkOrder::with(['client', 'technician'])
            ->whereNotNull('scheduled_at')
            ->whereIn('status', ['scheduled', 'in_progress']);

        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $orders = $query->orderBy('scheduled_at')->get();

        return view('admin.work_orders.map', compact('orders'));
    }
}
