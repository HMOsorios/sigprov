<?php

namespace App\Console\Commands;

use App\Services\MonitoringService;
use Illuminate\Console\Command;

class PingAllServers extends Command
{
    protected $signature = 'app:ping-all-servers';
    protected $description = 'Ping em todos os servidores e atualiza status';

    public function handle(MonitoringService $monitoring)
    {
        $this->line('Pingando servidores...');

        $results = $monitoring->pingAllServers();

        $online = collect($results)->filter(fn($r) => $r['alive'])->count();
        $offline = collect($results)->filter(fn($r) => !$r['alive'])->count();

        $this->info("Online: {$online}, Offline: {$offline}");
        return 0;
    }
}
