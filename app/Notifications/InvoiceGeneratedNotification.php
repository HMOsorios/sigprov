<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
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
        if (($prefs['sms'] ?? false) && ($notifiable->cellphone || $notifiable->phone)) {
            $channels[] = \App\Channels\SmsChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        $contract = $this->invoice->contract;

        return (new MailMessage)
            ->subject("Fatura {$this->invoice->invoice_number} gerada - SisProv")
            ->greeting("Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}!")
            ->line("Sua fatura {$this->invoice->invoice_number} no valor de R$ " .
                number_format($this->invoice->total, 2, ',', '.') . " foi gerada.")
            ->line("Vencimento: {$this->invoice->due_date->format('d/m/Y')}")
            ->line("Plano: {$contract?->plan?->name ?? '-'}")
            ->action('Baixar Boleto', route('client.invoices.show', $this->invoice))
            ->line('Obrigado por ser nosso cliente!');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return "Olá, {$client?->fantasy_name ?? $client?->name ?? 'Cliente'}! "
            . "Sua fatura {$this->invoice->invoice_number} de R$ "
            . number_format($this->invoice->total, 2, ',', '.')
            . " vence em {$this->invoice->due_date->format('d/m/Y')}. "
            . "Acesse o app para baixar o boleto.";
    }

    public function toSms(object $notifiable): string
    {
        $client = $notifiable instanceof \App\Models\Client ? $notifiable : null;
        return "{$client?->fantasy_name ?? $client?->name ?? ''}, "
            . "fatura {$this->invoice->invoice_number} de R$ "
            . number_format($this->invoice->total, 2, ',', '.')
            . " vence {$this->invoice->due_date->format('d/m/Y')}. SisProv";
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_generated',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total' => $this->invoice->total,
            'due_date' => $this->invoice->due_date->format('d/m/Y'),
            'message' => "Fatura {$this->invoice->invoice_number} gerada - R$ " .
                number_format($this->invoice->total, 2, ',', '.'),
        ];
    }
}
