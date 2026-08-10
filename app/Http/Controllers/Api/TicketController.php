<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['client', 'assignedTo', 'messages']);

        if ($request->user()->role?->name === 'client') {
            $clientIds = $request->user()->clients()->pluck('clients.id');
            $query->whereIn('client_id', $clientIds);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($tickets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'category' => ['required', 'in:technical,billing,commercial,installation,complaint,other'],
            'message' => ['required', 'string'],
        ]);

        $client = $request->user()->clients()->first();
        if (!$client) {
            return response()->json(['message' => 'Nenhum cliente vinculado.'], 404);
        }

        $ticket = Ticket::create([
            'client_id' => $client->id,
            'ticket_number' => 'TKT-' . now()->format('Ymd') . '-' . str_pad(Ticket::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'category' => $validated['category'],
            'status' => 'open',
            'created_by' => $request->user()->id,
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        return response()->json($ticket->load('messages'), 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load(['client', 'assignedTo', 'messages.user']);
        return response()->json($ticket);
    }

    public function reply(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $message = $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return response()->json($message, 201);
    }
}
