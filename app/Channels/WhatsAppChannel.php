<?php

namespace App\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    public function __construct(
        protected WhatsAppService $whatsapp,
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $to = $this->getRecipient($notifiable);
        if (!$to) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (is_string($message)) {
            $this->whatsapp->sendText($to, $message);
        } elseif (is_array($message)) {
            $method = $message['method'] ?? 'sendText';
            $params = $message['params'] ?? [];
            $this->whatsapp->$method($to, ...$params);
        }
    }

    protected function getRecipient(object $notifiable): ?string
    {
        if ($notifiable->cellphone ?? null) {
            return $notifiable->cellphone;
        }
        if ($notifiable->phone ?? null) {
            return $notifiable->phone;
        }
        return $notifiable->routeNotificationFor('whatsapp', null);
    }
}
