<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = Client::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('fantasy_name', 'like', "%{$search}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($personType = $request->get('person_type')) {
            $query->where('person_type', $personType);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');

        $clients = $query->orderBy($sortField, $sortDir)->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'fantasy_name' => ['nullable', 'string', 'max:200'],
            'cpf_cnpj' => ['required', 'string', 'max:18', 'unique:clients'],
            'rg_ie' => ['nullable', 'string', 'max:20'],
            'person_type' => ['required', 'in:pf,pj'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            'zipcode' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:200'],
            'address_number' => ['required', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:2'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'observations' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();
        $client = Client::create($validated);

        $this->auditService->logCreate('client', $client->id, "Cliente {$client->company_name} criado", $validated);

        return $this->redirectWith('admin.clients.index', 'Cliente cadastrado com sucesso!');
    }

    public function show(Client $client): View
    {
        $client->load(['contracts.plan', 'invoices' => function ($q) {
            $q->latest()->limit(10);
        }, 'tickets' => function ($q) {
            $q->latest()->limit(10);
        }, 'users']);

        $activeContracts = $client->contracts()->where('status', 'active')->count();
        $totalInvoiced = $client->invoices()->where('status', 'paid')->sum('total');
        $openTickets = $client->tickets()->open()->count();
        $links = $client->links()->with('server')->get();

        return view('admin.clients.show', compact(
            'client', 'activeContracts', 'totalInvoiced', 'openTickets', 'links'
        ));
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.form', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'fantasy_name' => ['nullable', 'string', 'max:200'],
            'cpf_cnpj' => ['required', 'string', 'max:18', 'unique:clients,cpf_cnpj,' . $client->id],
            'rg_ie' => ['nullable', 'string', 'max:20'],
            'person_type' => ['required', 'in:pf,pj'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            'zipcode' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:200'],
            'address_number' => ['required', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:2'],
            'status' => ['required', 'in:active,inactive,blocked,canceled'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'observations' => ['nullable', 'string'],
            'notification_preferences' => ['nullable', 'array'],
            'notification_preferences.mail' => ['nullable', 'boolean'],
            'notification_preferences.whatsapp' => ['nullable', 'boolean'],
            'notification_preferences.sms' => ['nullable', 'boolean'],
        ]);

        $oldValues = $client->toArray();
        $client->update($validated);

        $this->auditService->logUpdate('client', $client->id, "Cliente {$client->company_name} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.clients.index', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->contracts()->where('status', 'active')->exists()) {
            return $this->error('Cliente possui contratos ativos. Não é possível excluir.');
        }

        $this->auditService->logDelete('client', $client->id, "Cliente {$client->company_name} excluído", $client->toArray());
        $client->delete();

        return $this->redirectWith('admin.clients.index', 'Cliente excluído com sucesso!');
    }

    public function block(Client $client): RedirectResponse
    {
        $client->update(['status' => 'blocked']);
        $client->contracts()->where('status', 'active')->update(['status' => 'suspended']);

        return $this->redirectWith('admin.clients.show', 'Cliente bloqueado com sucesso!', 'warning');
    }

    public function unblock(Client $client): RedirectResponse
    {
        $client->update(['status' => 'active']);

        return $this->redirectWith('admin.clients.show', 'Cliente desbloqueado com sucesso!');
    }
}
