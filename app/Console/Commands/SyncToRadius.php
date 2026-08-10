<?php

namespace App\Console\Commands;

use App\Services\RadiusService;
use Illuminate\Console\Command;

class SyncToRadius extends Command
{
    protected $signature = 'app:sync-to-radius';
    protected $description = 'Sincroniza usuários PPPoE com FreeRADIUS';

    public function handle(RadiusService $radius)
    {
        $this->line('Sincronizando usuários com FreeRADIUS...');

        $result = $radius->syncUsers();

        $this->info("Sincronizados: {$result['synced']}, Erros: {$result['errors']}");

        return 0;
    }
}
