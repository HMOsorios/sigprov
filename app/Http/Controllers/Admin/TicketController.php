<?php

namespace App\Http\Controllers\Admin;

use App\Events\TicketCreated;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        $query = Ticket::with(['client', 'assignedTo']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignedTo = $request->get('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $tickets = $query->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $technicians = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'technician']);
        })->get();

        $counters = [
            'open' => Ticket::whereIn('status', ['open', 'in_progress', 'waiting_client'])->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'critical' => Ticket::where('priority', 'critical')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'technicians', 'counters'));
    }

    public function create(): View
    {
        $clients = Client::active()->orderBy('company_name')->get();
        $contracts = Contract::with('client')->where('status', 'active')->get();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'technician']);
        })->get();

        return view('admin.tickets.form', compact('clients', 'contracts', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'contract_id' => ['nullable', 'exists:contracts,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:200'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'category' => ['required', 'in:technical,billing,commercial,installation,complaint,other'],
            'message' => ['required', 'string'],
        ]);

        $validated['ticket_number'] = 'TKT-' . now()->format('Ymd') . '-' . str_pad(Ticket::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        $ticket = Ticket::create($validated);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_system' => false,
        ]);

        $this->auditService->logCreate('ticket', $ticket->id, "Chamado {$ticket->ticket_number} criado: {$ticket->subject}", $validated);

        TicketCreated::dispatch($ticket);

        return $this->redirectWith('admin.tickets.show', 'Chamado criado com sucesso!');
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['client', 'contract.plan', 'assignedTo', 'messages.user', 'creator']);
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'technician']);
        })->get();

        return view('admin.tickets.show', compact('ticket', 'users'));
    }

    public function edit(Ticket $ticket): View
    {
        $clients = Client::active()->orderBy('company_name')->get();
        $contracts = Contract::with('client')->where('status', 'active')->get();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'technician']);
        })->get();

        return view('admin.tickets.form', compact('ticket', 'clients', 'contracts', 'users'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'contract_id' => ['nullable', 'exists:contracts,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:200'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'category' => ['required', 'in:technical,billing,commercial,installation,complaint,other'],
            'status' => ['required', 'in:open,in_progress,waiting_client,resolved,closed'],
        ]);

        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }
        if ($validated['status'] === 'closed' && !$ticket->closed_at) {
            $validated['closed_at'] = now();
        }

        $oldValues = $ticket->toArray();
        $ticket->update($validated);

        $this->auditService->logUpdate('ticket', $ticket->id, "Chamado {$ticket->ticket_number} atualizado", $oldValues, $validated);

        return $this->redirectWith('admin.tickets.show', 'Chamado atualizado com sucesso!');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->messages()->delete();
        $ticket->delete();

        return $this->redirectWith('admin.tickets.index', 'Chamado excluído!');
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
            'is_internal' => ['boolean'],
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        if ($ticket->status === 'waiting_client') {
            $ticket->update(['status' => 'in_progress']);
        }

        return $this->redirectWith('admin.tickets.show', 'Resposta enviada!');
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => 'Chamado atribuído para ' . User::find($validated['assigned_to'])?->name,
            'is_system' => true,
        ]);

        return $this->redirectWith('admin.tickets.show', 'Chamado atribuído!');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed', 'closed_at' => now()]);

        return $this->redirectWith('admin.tickets.show', 'Chamado fechado!');
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'open', 'resolved_at' => null, 'closed_at' => null]);

        return $this->redirectWith('admin.tickets.show', 'Chamado reaberto!');
    }
}
