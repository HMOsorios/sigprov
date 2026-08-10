<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Link;
use App\Models\Server;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = Link::with(['contract.client', 'server']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pppoe_user', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('ont_serial', 'like', "%{$search}%")
                    ->orWhereHas('contract.client', function ($cq) use ($search) {
                        $cq->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($serverId = $request->get('server_id')) {
            $query->where('server_id', $serverId);
        }

        $links = $query->orderBy('created_at', 'desc')->paginate(15);
        $servers = Server::active()->get();

        return view('admin.links.index', compact('links', 'servers'));
    }

    public function create(): View
    {
        $contracts = Contract::with('client')->where('status', 'active')->get();
        $servers = Server::active()->where('type', 'router')->get();
        return view('admin.links.form', compact('contracts', 'servers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'server_id' => ['nullable', 'exists:servers,id'],
            'pppoe_user' => ['nullable', 'string', 'max:100', 'unique:links'],
            'pppoe_password' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'mac_address' => ['nullable', 'string', 'max:17'],
            'vlan' => ['nullable', 'string', 'max:10'],
            'ont_serial' => ['nullable', 'string', 'max:50'],
            'ont_brand' => ['nullable', 'string', 'max:50'],
            'ont_model' => ['nullable', 'string', 'max:50'],
            'cable_origin' => ['nullable', 'string', 'max:100'],
            'cable_drop' => ['nullable', 'string', 'max:100'],
            'splitter_location' => ['nullable', 'string', 'max:200'],
            'signal_rx' => ['nullable', 'integer'],
            'signal_tx' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['activated_at'] = now();

        $link = Link::create($validated);

        $this->auditService->logCreate('link', $link->id, "Link PPPoE {$link->pppoe_user} criado", $validated);

        return $this->redirectWith('admin.links.index', 'Link cadastrado com sucesso!');
    }

    public function show(Link $link): View
    {
        $link->load(['contract.client', 'contract.plan', 'server', 'logs' => function ($q) {
            $q->latest()->limit(30);
        }]);

        return view('admin.links.show', compact('link'));
    }

    public function edit(Link $link): View
    {
        $contracts = Contract::with('client')->where('status', 'active')->get();
        $servers = Server::active()->where('type', 'router')->get();
        return view('admin.links.form', compact('link', 'contracts', 'servers'));
    }

    public function update(Request $request, Link $link): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'server_id' => ['nullable', 'exists:servers,id'],
            'pppoe_user' => ['nullable', 'string', 'max:100', 'unique:links,pppoe_user,' . $link->id],
            'pppoe_password' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'mac_address' => ['nullable', 'string', 'max:17'],
            'vlan' => ['nullable', 'string', 'max:10'],
            'ont_serial' => ['nullable', 'string', 'max:50'],
            'ont_brand' => ['nullable', 'string', 'max:50'],
            'ont_model' => ['nullable', 'string', 'max:50'],
            'cable_origin' => ['nullable', 'string', 'max:100'],
            'cable_drop' => ['nullable', 'string', 'max:100'],
            'splitter_location' => ['nullable', 'string', 'max:200'],
            'signal_rx' => ['nullable', 'integer'],
            'signal_tx' => ['nullable', 'integer'],
            'status' => ['required', 'in:active,inactive,blocked,maintenance'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $link->toArray();
        $link->update($validated);

        $this->auditService->logUpdate('link', $link->id, "Link {$link->pppoe_user} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.links.index', 'Link atualizado com sucesso!');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $this->auditService->logDelete('link', $link->id, "Link {$link->pppoe_user} excluído", $link->toArray());
        $link->delete();

        return $this->redirectWith('admin.links.index', 'Link excluído com sucesso!');
    }

    public function block(Link $link): RedirectResponse
    {
        $link->update(['status' => 'blocked', 'blocked_at' => now()]);
        $link->logs()->create([
            'action' => 'blocked',
            'description' => 'Link bloqueado por ' . auth()->user()->name,
            'performed_by' => auth()->id(),
        ]);

        return $this->redirectWith('admin.links.show', 'Link bloqueado!', 'warning');
    }

    public function unblock(Link $link): RedirectResponse
    {
        $link->update(['status' => 'active', 'blocked_at' => null]);
        $link->logs()->create([
            'action' => 'unblocked',
            'description' => 'Link desbloqueado por ' . auth()->user()->name,
            'performed_by' => auth()->id(),
        ]);

        return $this->redirectWith('admin.links.show', 'Link desbloqueado!');
    }
}
