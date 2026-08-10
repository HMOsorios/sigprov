<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\RouterOsService;
use Illuminate\Console\Command;

class SyncRadiusToMikrotik extends Command
{
    protected $signature = 'app:sync-radius-to-mikrotik {--server= : ID do servidor específico}';
    protected $description = 'Sincroniza usuários PPPoE do BD para os roteadores MikroTik';

    public function handle(RouterOsService $routerOs)
    {
        $servers = Server::where('is_active', true)
            ->where('type', 'router')
            ->when($this->option('server'), fn($q, $id) => $q->where('id', $id))
            ->get();

        if ($servers->isEmpty()) {
            $this->warn('Nenhum servidor router encontrado.');
            return 0;
        }

        foreach ($servers as $server) {
            $this->line("Sincronizando {$server->name} ({$server->ip_address})...");

            $result = $routerOs->syncPppoeSecrets($server);

            if (($result['status'] ?? '') === 'error') {
                $this->error("Falha na conexão com {$server->name}");
                continue;
            }

            $this->info("  Sincronizados: {$result['synced']}, Erros: {$result['errors']}");
        }

        $this->info('Sincronização concluída.');
        return 0;
    }
}
