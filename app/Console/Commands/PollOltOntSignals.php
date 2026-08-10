<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\MonitoringService;
use Illuminate\Console\Command;

class PollOltOntSignals extends Command
{
    protected $signature = 'app:poll-olt-ont-signals {--server= : ID do servidor OLT específico}';
    protected $description = 'Consulta OLTs via SNMP/SSH e atualiza sinal óptico dos links';

    public function handle(MonitoringService $monitoring)
    {
        $servers = Server::where('is_active', true)
            ->when($this->option('server'), fn($q, $id) => $q->where('id', $id))
            ->get()
            ->filter(fn($s) => str_contains($s->type ?? '', 'olt'));

        if ($servers->isEmpty()) {
            $this->warn('Nenhuma OLT encontrada.');
            return 0;
        }

        foreach ($servers as $server) {
            $this->line("Polling OLT {$server->name}...");

            $results = $monitoring->pollOltSignals($server);

            if (isset($results['status']) && $results['status'] === 'not_olt') {
                $this->warn("  {$server->name} não é uma OLT.");
                continue;
            }

            $this->info("  {$server->name}: " . count($results) . " ONTs consultadas.");
        }

        $this->info('Polling concluído.');
        return 0;
    }
}
