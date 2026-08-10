<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        $tickets = Ticket::whereIn('client_id', $clientIds)
            ->with('assignedTo')
            ->latest()
            ->paginate(15);

        $counters = [
            'open' => Ticket::whereIn('client_id', $clientIds)->open()->count(),
            'resolved' => Ticket::whereIn('client_id', $clientIds)->where('status', 'resolved')->count(),
            'closed' => Ticket::whereIn('client_id', $clientIds)->where('status', 'closed')->count(),
        ];

        return view('client.tickets.index', compact('tickets', 'counters'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $contracts = $user->clients->flatMap->activeContracts;

        return view('client.tickets.form', compact('contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        $validated = $request->validate([
            'client_id' => ['required', 'in:' . $clientIds->implode(',')],
            'contract_id' => ['nullable', 'exists:contracts,id'],
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
        ]);

        return redirect()->route('client.tickets.show', $ticket)
            ->with('success', 'Chamado criado com sucesso!');
    }

    public function show(Ticket $ticket): View
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        if (!in_array($ticket->client_id, $clientIds->toArray())) {
            abort(403);
        }

        $ticket->load(['messages.user', 'assignedTo', 'contract.plan']);

        return view('client.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        if (!in_array($ticket->client_id, $clientIds->toArray())) {
            abort(403);
        }

        $validated = $request->validate(['message' => ['required', 'string']]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        if ($ticket->status === 'waiting_client') {
            $ticket->update(['status' => 'open']);
        }

        return redirect()->back()->with('success', 'Resposta enviada!');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();
        $clientIds = $user->clients->pluck('id');

        if (!in_array($ticket->client_id, $clientIds->toArray())) {
            abort(403);
        }

        $ticket->update(['status' => 'closed', 'closed_at' => now()]);

        return redirect()->back()->with('success', 'Chamado fechado!');
    }
}
