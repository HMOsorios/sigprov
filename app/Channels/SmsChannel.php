<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $to = $this->getRecipient($notifiable);
        if (!$to) {
            return;
        }

        $message = $notification->toSms($notifiable);
        if (!$message) {
            return;
        }

        $provider = config('services.sms.provider', 'log');

        match ($provider) {
            'twilio' => $this->sendTwilio($to, $message),
            'zenvia' => $this->sendZenvia($to, $message),
            default => $this->logMessage($to, $message),
        };
    }

    protected function sendTwilio(string $to, string $message): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if (!$sid || !$token) {
            Log::warning('Twilio not configured');
            return;
        }

        Http::withBasicAuth($sid, $token)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $to,
                'Body' => $message,
            ]);
    }

    protected function sendZenvia(string $to, string $message): void
    {
        $token = config('services.zenvia.token');
        $from = config('services.zenvia.from', 'SisProv');

        if (!$token) {
            Log::warning('Zenvia not configured');
            return;
        }

        Http::withToken($token)
            ->post('https://api.zenvia.com/v1/channels/sms/messages', [
                'from' => $from,
                'to' => $to,
                'contents' => [['type' => 'text', 'text' => $message]],
            ]);
    }

    protected function logMessage(string $to, string $message): void
    {
        Log::info('SMS sent', compact('to', 'message'));
    }

    protected function getRecipient(object $notifiable): ?string
    {
        if ($notifiable->cellphone ?? null) {
            return preg_replace('/\D/', '', $notifiable->cellphone);
        }
        if ($notifiable->phone ?? null) {
            return preg_replace('/\D/', '', $notifiable->phone);
        }
        return $notifiable->routeNotificationFor('sms', null);
    }
}
