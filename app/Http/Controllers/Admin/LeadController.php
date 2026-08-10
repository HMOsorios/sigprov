<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:developer,admin');
    }

    public function index(Request $request): View
    {
        $query = Lead::with('assignedTo');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('cellphone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $leads = $query->latest()->paginate(20);
        $statuses = ['new', 'contacted', 'proposal', 'negotiation', 'won', 'lost'];
        $sources = Lead::select('source')->distinct()->whereNotNull('source')->pluck('source');
        $staff = User::whereIn('role_id', function ($q) {
            $q->select('id')->from('roles')->whereIn('name', ['developer', 'admin', 'administrativo']);
        })->orderBy('name')->get();

        return view('admin.leads.index', compact('leads', 'statuses', 'sources', 'staff'));
    }

    public function kanban(): View
    {
        $statuses = ['new', 'contacted', 'proposal', 'negotiation', 'won', 'lost'];
        $leadsByStatus = [];

        foreach ($statuses as $status) {
            $leadsByStatus[$status] = Lead::with('assignedTo')
                ->where('status', $status)
                ->latest()
                ->get();
        }

        $staff = User::whereIn('role_id', function ($q) {
            $q->select('id')->from('roles')->whereIn('name', ['developer', 'admin', 'administrativo']);
        })->orderBy('name')->get();

        return view('admin.leads.kanban', compact('leadsByStatus', 'staff'));
    }

    public function create(): View
    {
        $plans = Plan::where('status', 'active')->orderBy('name')->get();
        $staff = User::whereIn('role_id', function ($q) {
            $q->select('id')->from('roles')->whereIn('name', ['developer', 'admin', 'administrativo']);
        })->orderBy('name')->get();

        return view('admin.leads.create', compact('plans', 'staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'interest_plan' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:50',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['status'] ??= 'new';

        Lead::create($validated);

        AuditService::log('lead_created', 'Lead criado: ' . $validated['name']);

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead cadastrado com sucesso.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['assignedTo', 'convertedClient']);
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $plans = Plan::where('status', 'active')->orderBy('name')->get();
        $staff = User::whereIn('role_id', function ($q) {
            $q->select('id')->from('roles')->whereIn('name', ['developer', 'admin', 'administrativo']);
        })->orderBy('name')->get();

        return view('admin.leads.edit', compact('lead', 'plans', 'staff'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'interest_plan' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:50',
            'status' => 'required|string|max:30',
            'notes' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($validated);

        AuditService::log('lead_updated', 'Lead atualizado: ' . $validated['name']);

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead atualizado com sucesso.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        AuditService::log('lead_deleted', 'Lead excluído: ' . $lead->name);

        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead excluído com sucesso.');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate(['status' => 'required|string|max:30']);

        $lead->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $lead->status]);
    }

    public function convert(Lead $lead): View
    {
        if ($lead->converted_client_id) {
            return redirect()->route('admin.leads.show', $lead)
                ->with('info', 'Lead já convertido.');
        }

        $plans = Plan::where('status', 'active')->orderBy('name')->get();

        return view('admin.leads.convert', compact('lead', 'plans'));
    }

    public function doConvert(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->converted_client_id) {
            return redirect()->route('admin.leads.show', $lead)
                ->with('info', 'Lead já convertido.');
        }

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:200',
            'fantasy_name' => 'nullable|string|max:200',
            'cpf_cnpj' => 'required|string|max:20|unique:clients,cpf_cnpj',
            'person_type' => 'required|in:pf,pj',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'zipcode' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:200',
            'address_number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'active';
        $validated['lead_source'] = $lead->source;
        $validated['observations'] = $validated['notes'] ?? null;
        unset($validated['notes']);

        $client = Client::create($validated);

        $lead->update([
            'converted_client_id' => $client->id,
            'converted_at' => now(),
            'status' => 'won',
        ]);

        AuditService::log('lead_converted', "Lead {$lead->name} convertido para cliente {$client->id}");

        return redirect()->route('admin.contracts.create', ['client_id' => $client->id])
            ->with('success', 'Lead convertido em cliente com sucesso! Crie o contrato agora.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:leads,id',
            'status' => 'required|string|max:30',
        ]);

        Lead::whereIn('id', $validated['ids'])->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', count($validated['ids']) . ' leads atualizados.');
    }
}
