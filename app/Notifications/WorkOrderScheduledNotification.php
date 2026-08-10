<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WorkOrder $workOrder,
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
            ->subject("Ordem de Serviço agendada - SisProv")
            ->greeting("Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}!")
            ->line("Uma ordem de serviço foi agendada para sua instalação.")
            ->line("Tipo: {$this->workOrder->type_label}")
            ->line("Data: {$this->workOrder->scheduled_at?->format('d/m/Y H:i')}")
            ->line("Técnico: {$this->workOrder->technician?->name ?? 'A definir'}");
    }

    public function toWhatsApp(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        $date = $this->workOrder->scheduled_at?->format('d/m/Y H:i');
        return "{$client?->fantasy_name ?? $client?->name ?? ''}, "
            . "sua ordem de serviço foi agendada para {$date}.";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'work_order_scheduled',
            'work_order_id' => $this->workOrder->id,
            'scheduled_at' => $this->workOrder->scheduled_at?->toIso8601String(),
            'message' => "OS {$this->workOrder->numero} agendada para " .
                $this->workOrder->scheduled_at?->format('d/m/Y H:i'),
        ];
    }
}
