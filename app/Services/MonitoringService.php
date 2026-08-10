<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Server;
use App\Models\LinkLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public function pingServer(Server $server): array
    {
        $host = $server->ip_address ?: $server->hostname;
        $timeout = 5;

        $start = microtime(true);
        $cmd = PHP_OS_FAMILY === 'Windows'
            ? "ping -n 1 -w {$timeout}000 {$host}"
            : "ping -c 1 -W {$timeout} {$host}";

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        $latency = null;
        if ($returnCode === 0) {
            $pingMs = PHP_OS_FAMILY === 'Windows'
                ? preg_match('/tempo[=<]([\d]+)ms/i', implode(' ', $output), $m)
                : preg_match('/time=([\d.]+)\s*ms/', implode(' ', $output), $m);

            if ($pingMs) {
                $latency = (float) $m[1];
            }
        }

        $elapsed = round((microtime(true) - $start) * 1000, 0);

        return [
            'host' => $host,
            'alive' => $returnCode === 0,
            'latency_ms' => $latency ?? $elapsed,
            'output' => implode("\n", $output),
        ];
    }

    public function pingAllServers(): array
    {
        $servers = Server::where('is_monitored', true)->where('is_active', true)->get();
        $results = [];

        foreach ($servers as $server) {
            $result = $this->pingServer($server);

            $server->update([
                'last_ping_at' => Carbon::now(),
                'status' => $result['alive'] ? 'online' : 'offline',
            ]);

            $server->logs()->create([
                'action' => $result['alive'] ? 'ping_ok' : 'ping_fail',
                'description' => "Ping: {$result['latency_ms']}ms",
                'metadata' => $result,
            ]);

            $results[$server->hostname ?: $server->ip_address] = $result;
        }

        return $results;
    }

    public function pollOltSignals(Server $server): array
    {
        if (!str_contains($server->type ?? '', 'olt')) {
            return ['status' => 'not_olt'];
        }

        $config = $server->monitoring_config ?? [];
        $results = [];

        if (!empty($config['snmp_community'])) {
            $results = $this->snmpPollOnt($server, $config);
        } elseif ($server->username) {
            $results = $this->sshPollOnt($server);
        }

        foreach ($results as $result) {
            Link::where('id', $result['link_id'] ?? 0)
                ->update([
                    'signal_rx' => $result['signal_rx'] ?? null,
                    'signal_tx' => $result['signal_tx'] ?? null,
                ]);
        }

        return $results;
    }

    protected function snmpPollOnt(Server $server, array $config = []): array
    {
        $results = [];

        if (!function_exists('snmp2_get')) {
            Log::warning('SNMP extension not installed');
            return $results;
        }

        try {
            $oids = [
                'signal_rx' => '.1.3.6.1.4.1.2011.6.128.1.1.2.46.1.10',
                'signal_tx' => '.1.3.6.1.4.1.2011.6.128.1.1.2.46.1.9',
                'temperature' => '.1.3.6.1.4.1.2011.6.128.1.1.2.46.1.8',
                'voltage' => '.1.3.6.1.4.1.2011.6.128.1.1.2.46.1.7',
            ];

            $links = Link::where('server_id', $server->id)
                ->whereNotNull('ont_serial')
                ->get();

            foreach ($links as $link) {
                $result = ['link_id' => $link->id];
                $host = $server->ip_address ?: $server->hostname;
                $community = $config['snmp_community'] ?? 'public';

                $ifIndex = $this->getOntIfIndex($server, $link->ont_serial);
                if (!$ifIndex) {
                    continue;
                }

                $signalRx = @snmp2_get(
                    $host,
                    $community,
                    "{$oids['signal_rx']}.{$ifIndex}",
                    1000000
                );
                if ($signalRx !== false) {
                    $result['signal_rx'] = (float) filter_var($signalRx, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }

                $signalTx = @snmp2_get(
                    $host,
                    $community,
                    "{$oids['signal_tx']}.{$ifIndex}",
                    1000000
                );
                if ($signalTx !== false) {
                    $result['signal_tx'] = (float) filter_var($signalTx, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }

                if (isset($result['signal_rx']) && $result['signal_rx'] < -27) {
                    Log::warning("Low signal on link {$link->id}: {$result['signal_rx']}dBm");
                }

                $results[] = $result;
            }
        } catch (\Exception $e) {
            Log::error("SNMP poll error on {$server->host}: {$e->getMessage()}");
        }

        return $results;
    }

    protected function sshPollOnt(Server $server): array
    {
        $results = [];

        try {
            $links = Link::where('server_id', $server->id)
                ->whereNotNull('onu_serial')
                ->get();

            foreach ($links as $link) {
                $result = ['link_id' => $link->id];

                $command = "/interface gpon onu get {$link->onu_serial} rx-power";
                $output = $this->sshExec($server, $command);

                if (preg_match('/rx-power:\s+([\-\d.]+)/', $output, $m)) {
                    $result['signal_rx'] = (float) $m[1];
                }

                if (isset($result['signal_rx']) && $result['signal_rx'] < -27) {
                    Log::warning("Low signal on link {$link->id}: {$result['signal_rx']}dBm");
                }

                $results[] = $result;
            }
        } catch (\Exception $e) {
            Log::error("SSH poll error on {$server->host}: {$e->getMessage()}");
        }

        return $results;
    }

    protected function getOntIfIndex(Server $server, string $serial): ?int
    {
        try {
            $config = $server->monitoring_config ?? [];
            $host = $server->ip_address ?: $server->hostname;
            $community = $config['snmp_community'] ?? 'public';

            $oid = ".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.4";
            $result = @snmp2_walk($host, $community, $oid, 1000000);

            if ($result === false) {
                return null;
            }

            foreach ($result as $oidFull => $value) {
                if (str_contains($value, $serial)) {
                    preg_match('/\.(\d+)$/', $oidFull, $m);
                    return (int) $m[1];
                }
            }
        } catch (\Exception $e) {
            Log::error("ONT ifIndex error: {$e->getMessage()}");
        }

        return null;
    }

    protected function sshExec(Server $server, string $command): string
    {
        $host = $server->ip_address ?: $server->hostname;
        $user = $server->username;
        $pass = $server->encrypted_password ?? '';
        $port = $server->port ?? 22;

        if (PHP_OS_FAMILY === 'Windows') {
            $sshCmd = "echo {$pass} | ssh -o StrictHostKeyChecking=no -p {$port} {$user}@{$host} {$command} 2>&1";
        } else {
            $sshCmd = "sshpass -p '{$pass}' ssh -o StrictHostKeyChecking=no -p {$port} {$user}@{$host} '{$command}' 2>&1";
        }

        $output = [];
        exec($sshCmd, $output, $returnCode);

        return implode("\n", $output);
    }
}
