<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketNotification implements ShouldQueue
{
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;
        $client = $ticket->client;

        if (!$client) {
            return;
        }

        activity('notification')->log("Ticket #{$ticket->id} criado por {$client->name_display}");
    }
}
