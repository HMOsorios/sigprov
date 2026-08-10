<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Contract $contract,
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
            ->subject("Contrato suspenso - {$this->contract->contract_number}")
            ->greeting("Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}!")
            ->line("Seu contrato {$this->contract->contract_number} foi suspenso por inadimplência.")
            ->line("Regularize sua fatura para reativar o serviço.")
            ->action('Regularizar', route('client.invoices.index'));
    }

    public function toWhatsApp(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return "{$client?->fantasy_name ?? $client?->name ?? ''}, "
            . "seu contrato foi suspenso. Regularize no app SisProv para voltar a navegar.";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contract_suspended',
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'message' => "Contrato {$this->contract->contract_number} suspenso por inadimplência",
        ];
    }
}
