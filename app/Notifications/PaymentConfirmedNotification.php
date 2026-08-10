<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
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
            ->subject("Pagamento confirmado - {$this->invoice->invoice_number}")
            ->greeting("Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}!")
            ->line("O pagamento da fatura {$this->invoice->invoice_number} no valor de R$ " .
                number_format($this->invoice->total, 2, ',', '.') . " foi confirmado.")
            ->line("Obrigado por manter seu pagamento em dia!");
    }

    public function toWhatsApp(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return "{$client?->fantasy_name ?? $client?->name ?? ''}, "
            . "o pagamento de R$ " . number_format($this->invoice->total, 2, ',', '.')
            . " foi confirmado. Obrigado! SisProv";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmed',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total' => $this->invoice->total,
            'message' => "Pagamento confirmado - {$this->invoice->invoice_number}",
        ];
    }
}
