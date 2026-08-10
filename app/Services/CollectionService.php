<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class CollectionService
{
    public function process(): int
    {
        $rules = config('collection.rules', []);
        $sent = 0;

        foreach ($rules as $rule) {
            $targetDate = now()->subDays($rule['days']);

            $invoices = Invoice::where('status', 'overdue')
                ->whereDate('due_date', $targetDate)
                ->with('client')
                ->get();

            foreach ($invoices as $invoice) {
                $this->send($invoice, $rule);
                $sent++;
            }
        }

        return $sent;
    }

    public function send(Invoice $invoice, array $rule): void
    {
        $client = $invoice->client;
        if (!$client) {
            return;
        }

        $channels = $rule['channels'] ?? ['email'];

        foreach ($channels as $channel) {
            try {
                match ($channel) {
                    'email' => $this->sendEmail($invoice, $rule),
                    'whatsapp' => $this->sendWhatsApp($invoice, $rule),
                    'sms' => $this->sendSms($invoice, $rule),
                    'system' => $this->sendSystemNotification($invoice, $rule),
                    default => null,
                };
            } catch (\Exception $e) {
                Log::error("Collection {$channel} error", [
                    'invoice' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $log = sprintf(
            '[D%d] %s — canais: %s — enviado em %s',
            $rule['days'],
            $rule['label'],
            implode(',', $channels),
            now()->toDateTimeString()
        );

        $invoice->notes = trim(($invoice->notes ?? '') . "\n" . $log);
        $invoice->save();
    }

    protected function sendEmail(Invoice $invoice, array $rule): void
    {
        $client = $invoice->client;
        if (!$client?->email) {
            return;
        }

        $template = $this->getTemplate($rule, 'email');

        $message = strtr($template, [
            '{nome}' => $client->name_display,
            '{fatura}' => $invoice->invoice_number,
            '{vencimento}' => $invoice->due_date->format('d/m/Y'),
            '{valor}' => $invoice->total_formatted,
            '{dias_atraso}' => (string) now()->diffInDays($invoice->due_date),
            '{link_pagamento}' => route('admin.invoices.show', $invoice),
        ]);

        Log::info("Collection Email", [
            'to' => $client->email,
            'subject' => "{$rule['label']} - Fatura {$invoice->invoice_number}",
            'body' => $message,
        ]);
    }

    protected function sendWhatsApp(Invoice $invoice, array $rule): void
    {
        $client = $invoice->client;
        $phone = $client?->cellphone ?: $client?->phone;
        if (!$phone) {
            return;
        }

        $template = $this->getTemplate($rule, 'whatsapp');

        $message = strtr($template, [
            '{nome}' => $client->name_display,
            '{fatura}' => $invoice->invoice_number,
            '{valor}' => $invoice->total_formatted,
            '{dias_atraso}' => (string) now()->diffInDays($invoice->due_date),
        ]);

        Log::info("Collection WhatsApp", [
            'to' => $phone,
            'message' => $message,
        ]);
    }

    protected function sendSms(Invoice $invoice, array $rule): void
    {
        $client = $invoice->client;
        $phone = $client?->cellphone ?: $client?->phone;
        if (!$phone) {
            return;
        }

        $template = $this->getTemplate($rule, 'sms');

        $message = strtr($template, [
            '{nome}' => $client->name_display,
            '{fatura}' => $invoice->invoice_number,
            '{valor}' => $invoice->total_formatted,
            '{dias_atraso}' => (string) now()->diffInDays($invoice->due_date),
        ]);

        Log::info("Collection SMS", [
            'to' => $phone,
            'message' => $message,
        ]);
    }

    protected function sendSystemNotification(Invoice $invoice, array $rule): void
    {
        $client = $invoice->client;
        $user = $client?->user;
        if (!$user) {
            return;
        }

        $user->notifications()->create([
            'title' => $rule['label'],
            'message' => strtr($this->getTemplate($rule, 'system'), [
                '{fatura}' => $invoice->invoice_number,
                '{valor}' => $invoice->total_formatted,
            ]),
            'type' => 'collection',
            'link' => route('client.invoices.show', $invoice),
        ]);
    }

    protected function getTemplate(array $rule, string $channel): string
    {
        $templates = $rule['templates'] ?? [];
        $key = "{$channel}_body";

        return $templates[$key]
            ?? config("collection.defaults.{$channel}", '');
    }
}
