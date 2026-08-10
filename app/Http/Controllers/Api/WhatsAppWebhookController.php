<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Ticket;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsapp,
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response()->json(['challenge' => $challenge]);
        }

        return response()->json(['error' => 'Invalid token'], 403);
    }

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        $message = $this->whatsapp->processWebhook($payload);

        if (!$message) {
            return response()->json(['status' => 'ignored']);
        }

        $client = Client::where('cellphone', 'like', "%{$message['from']}")
            ->orWhere('phone', 'like', "%{$message['from']}")
            ->first();

        if (!$client) {
            return response()->json(['status' => 'client_not_found']);
        }

        Ticket::create([
            'client_id' => $client->id,
            'subject' => 'Mensagem via WhatsApp',
            'priority' => 'medium',
            'category' => 'commercial',
            'status' => 'open',
            'created_by' => $client->users()->first()?->id,
        ]);

        return response()->json(['status' => 'ticket_created']);
    }
}
