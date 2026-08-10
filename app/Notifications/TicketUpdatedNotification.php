<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $action,
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = ['database'];

        if (($prefs['mail'] ?? true) && $notifiable->email) {
            $channels[] = 'mail';
        }
        if (($prefs['whatsapp'] ?? true) && ($notifiable->cellphone || $notifiable->phone)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return (new MailMessage)
            ->subject("Ticket {$this->ticket->ticket_number} atualizado - SisProv")
            ->greeting("Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}!")
            ->line("Seu ticket {$this->ticket->ticket_number} foi atualizado.")
            ->line("Assunto: {$this->ticket->subject}")
            ->line("Status: {$this->ticket->status_label}")
            ->action('Ver Ticket', route('client.tickets.show', $this->ticket));
    }

    public function toWhatsApp(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return "{$client?->fantasy_name ?? $client?->name ?? ''}, "
            . "seu ticket {$this->ticket->ticket_number} foi atualizado para "
            . "\"{$this->ticket->status_label}\". Acesse o app SisProv.";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_updated',
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'action' => $this->action,
            'message' => "Ticket {$this->ticket->ticket_number}: {$this->action}",
        ];
    }
}
