<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\RouterOsService;
use Illuminate\Console\Command;

class ApplyBandwidthProfiles extends Command
{
    protected $signature = 'app:apply-bandwidth-profiles {--server= : ID do servidor específico}';
    protected $description = 'Aplica filas/queues de banda conforme o plano de cada contrato';

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
            $this->line("Aplicando perfis de banda em {$server->name}...");

            $result = $routerOs->applyBandwidthProfiles($server);

            if (($result['status'] ?? '') === 'error') {
                $this->error("Falha na conexão com {$server->name}");
                continue;
            }

            $this->info("  Aplicados: {$result['applied']}, Erros: {$result['errors']}");
        }

        $this->info('Perfis de banda aplicados com sucesso.');
        return 0;
    }
}
