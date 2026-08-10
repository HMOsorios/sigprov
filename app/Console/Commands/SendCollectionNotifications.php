<?php

namespace App\Console\Commands;

use App\Services\CollectionService;
use Illuminate\Console\Command;

class SendCollectionNotifications extends Command
{
    protected $signature = 'app:send-collection-notifications';
    protected $description = 'Dispara notificações da régua de cobrança para faturas vencidas';

    public function handle(CollectionService $service)
    {
        $sent = $service->process();

        $this->info("Notificações da régua processadas: {$sent}.");
        return 0;
    }
}
