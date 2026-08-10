<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\ContractSuspended;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Plan;
use App\Services\AuditService;
use App\Services\ContractSigningService;
use App\Services\FidelityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = Contract::with(['client', 'plan']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($planId = $request->get('plan_id')) {
            $query->where('plan_id', $planId);
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(15);
        $plans = Plan::active()->get();

        return view('admin.contracts.index', compact('contracts', 'plans'));
    }

    public function create(): View
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $plans = Plan::active()->orderBy('name')->get();
        return view('admin.contracts.form', compact('clients', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'due_day' => ['required', 'integer', 'between:1,31'],
            'signed_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'installation_address' => ['required', 'string', 'max:200'],
            'installation_zipcode' => ['required', 'string', 'max:10'],
            'installation_neighborhood' => ['required', 'string', 'max:100'],
            'installation_city' => ['required', 'string', 'max:100'],
            'installation_state' => ['required', 'string', 'max:2'],
            'installation_complement' => ['nullable', 'string', 'max:100'],
            'installation_latitude' => ['nullable', 'string', 'max:20'],
            'installation_longitude' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            'minimum_duration_months' => ['nullable', 'integer', 'min:0'],
            'cancellation_fine_formula' => ['nullable', 'string', 'max:50'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $client = Client::findOrFail($validated['client_id']);

        $validated['contract_number'] = 'CTR-' . now()->format('Ymd') . '-' . str_pad(Contract::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_value'] = $validated['discount_value'] ?? 0;

        $contract = Contract::create($validated);

        $this->auditService->logCreate('contract', $contract->id, "Contrato {$contract->contract_number} criado para {$client->company_name}", $validated);

        return $this->redirectWith('admin.contracts.index', 'Contrato criado com sucesso!');
    }

    public function show(Contract $contract): View
    {
        $contract->load(['client', 'plan', 'invoices' => function ($q) {
            $q->latest()->limit(12);
        }, 'links.server', 'tickets' => function ($q) {
            $q->latest()->limit(5);
        }]);

        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $plans = Plan::active()->orderBy('name')->get();
        return view('admin.contracts.form', compact('contract', 'clients', 'plans'));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'status' => ['required', 'in:active,suspended,canceled,expired'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'due_day' => ['required', 'integer', 'between:1,31'],
            'signed_price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'installation_address' => ['required', 'string', 'max:200'],
            'installation_zipcode' => ['required', 'string', 'max:10'],
            'installation_neighborhood' => ['required', 'string', 'max:100'],
            'installation_city' => ['required', 'string', 'max:100'],
            'installation_state' => ['required', 'string', 'max:2'],
            'installation_complement' => ['nullable', 'string', 'max:100'],
            'installation_latitude' => ['nullable', 'string', 'max:20'],
            'installation_longitude' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            'minimum_duration_months' => ['nullable', 'integer', 'min:0'],
            'cancellation_fine_formula' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_value'] = $validated['discount_value'] ?? 0;

        $oldValues = $contract->toArray();
        $contract->update($validated);

        $this->auditService->logUpdate('contract', $contract->id, "Contrato {$contract->contract_number} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.contracts.index', 'Contrato atualizado com sucesso!');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        if ($contract->invoices()->where('status', 'paid')->exists()) {
            return $this->error('Contrato possui faturas pagas. Não é possível excluir.');
        }

        $this->auditService->logDelete('contract', $contract->id, "Contrato {$contract->contract_number} excluído", $contract->toArray());
        $contract->delete();

        return $this->redirectWith('admin.contracts.index', 'Contrato excluído com sucesso!');
    }

    public function suspend(Contract $contract): RedirectResponse
    {
        $contract->update(['status' => 'suspended']);
        $contract->links()->update(['status' => 'blocked']);

        ContractSuspended::dispatch($contract);

        return $this->redirectWith('admin.contracts.show', 'Contrato suspenso com sucesso!', 'warning');
    }

    public function reactivate(Contract $contract): RedirectResponse
    {
        $contract->update(['status' => 'active']);

        return $this->redirectWith('admin.contracts.show', 'Contrato reativado com sucesso!');
    }

    public function sendForSigning(Contract $contract, ContractSigningService $signing): RedirectResponse
    {
        $result = $signing->sendForSigning($contract);

        $message = $result['status'] === 'error'
            ? 'Erro ao enviar para assinatura: ' . ($result['message'] ?? '')
            : 'Contrato enviado para assinatura digital.';

        return $this->redirectWith('admin.contracts.show', $message, $result['status'] === 'error' ? 'error' : 'success');
    }

    public function markSigned(Contract $contract): RedirectResponse
    {
        $contract->update([
            'signature_status' => 'signed',
            'signed_at' => now(),
        ]);

        return $this->redirectWith('admin.contracts.show', 'Contrato marcado como assinado.');
    }

    public function calculateFine(Contract $contract, FidelityService $fidelity): JsonResponse
    {
        $fine = $fidelity->calculateCancellationFine($contract);
        $remaining = $fidelity->getMonthsRemaining($contract);
        $elapsed = $fidelity->getMonthsElapsed($contract);

        return response()->json([
            'fine' => round($fine, 2),
            'fine_formatted' => 'R$ ' . number_format($fine, 2, ',', '.'),
            'months_remaining' => $remaining,
            'months_elapsed' => $elapsed,
        ]);
    }
}
