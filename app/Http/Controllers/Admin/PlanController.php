<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(): View
    {
        $plans = Plan::orderBy('order')->orderBy('name')->paginate(15);
        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.plans.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'download_speed' => ['required', 'numeric', 'min:0'],
            'upload_speed' => ['required', 'numeric', 'min:0'],
            'speed_unit' => ['required', 'in:mbps,gbps'],
            'monthly_traffic' => ['nullable', 'integer', 'min:0'],
            'traffic_type' => ['required', 'in:unlimited,limited,fup'],
            'price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'contract_duration' => ['required', 'integer', 'min:1'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,semiannual,annual'],
            'max_connections' => ['required', 'integer', 'min:1'],
            'technology' => ['nullable', 'string', 'max:50'],
            'has_static_ip' => ['boolean'],
            'static_ip_qty' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'json'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['has_static_ip'] = $request->boolean('has_static_ip');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['features'] = $request->features ? json_decode($request->features, true) : null;

        $plan = Plan::create($validated);

        $this->auditService->logCreate('plan', $plan->id, "Plano {$plan->name} criado", $validated);

        return $this->redirectWith('admin.plans.index', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'download_speed' => ['required', 'numeric', 'min:0'],
            'upload_speed' => ['required', 'numeric', 'min:0'],
            'speed_unit' => ['required', 'in:mbps,gbps'],
            'monthly_traffic' => ['nullable', 'integer', 'min:0'],
            'traffic_type' => ['required', 'in:unlimited,limited,fup'],
            'price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'contract_duration' => ['required', 'integer', 'min:1'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,semiannual,annual'],
            'max_connections' => ['required', 'integer', 'min:1'],
            'technology' => ['nullable', 'string', 'max:50'],
            'has_static_ip' => ['boolean'],
            'static_ip_qty' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'json'],
        ]);

        $validated['has_static_ip'] = $request->boolean('has_static_ip');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['features'] = $request->features ? json_decode($request->features, true) : null;

        $oldValues = $plan->toArray();
        $plan->update($validated);

        $this->auditService->logUpdate('plan', $plan->id, "Plano {$plan->name} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.plans.index', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->contracts()->exists()) {
            return $this->error('Plano possui contratos vinculados. Não é possível excluir.');
        }

        $this->auditService->logDelete('plan', $plan->id, "Plano {$plan->name} excluído", $plan->toArray());
        $plan->delete();

        return $this->redirectWith('admin.plans.index', 'Plano excluído com sucesso!');
    }
}
