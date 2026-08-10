<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Plan;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

class RouterOsService
{
    protected ?\Socket $socket = null;
    protected bool $connected = false;
    protected array $server;

    public function connect(Server $server): bool
    {
        $monitoringConfig = $server->monitoring_config ?? [];
        $apiPort = $monitoringConfig['api_port'] ?? 8728;

        $this->server = [
            'host' => $server->ip_address ?: $server->hostname,
            'port' => $apiPort,
            'username' => $server->username,
            'password' => $server->encrypted_password ?? '',
        ];

        try {
            $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (!$this->socket) {
                throw new \RuntimeException('Failed to create socket');
            }

            @socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 5, 'usec' => 0]);
            @socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 5, 'usec' => 0]);

            $connected = @socket_connect(
                $this->socket,
                $this->server['host'],
                $this->server['port']
            );

            if (!$connected) {
                throw new \RuntimeException('Connection failed');
            }

            $this->write('/login');
            $this->write("name={$this->server['username']}");
            $this->write("password={$this->server['password']}");

            $trap = $this->read();
            if (!empty($trap) && str_contains(implode(' ', $trap), 'failure')) {
                $this->disconnect();
                throw new \RuntimeException('Login failed');
            }

            $this->connected = true;
            return true;
        } catch (\Exception $e) {
            Log::error("RouterOS connection error: {$e->getMessage()}", [
                'host' => $server->host,
            ]);
            $this->disconnect();
            return false;
        }
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            $this->write('/quit');
            socket_close($this->socket);
        }
        $this->socket = null;
        $this->connected = false;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function addPppoeUser(string $username, string $password, string $service = 'pppoe'): array
    {
        return $this->command('/ppp/secret/add', [
            "name={$username}",
            "password={$password}",
            "service={$service}",
            "disabled=no",
        ]);
    }

    public function removePppoeUser(string $username): array
    {
        $id = $this->findPppoeUserId($username);
        if (!$id) {
            return ['status' => 'not_found'];
        }

        return $this->command('/ppp/secret/remove', [".id={$id}"]);
    }

    public function enableUser(string $username): array
    {
        $id = $this->findPppoeUserId($username);
        if (!$id) {
            return ['status' => 'not_found'];
        }

        return $this->command('/ppp/secret/enable', [".id={$id}"]);
    }

    public function disableUser(string $username): array
    {
        $id = $this->findPppoeUserId($username);
        if (!$id) {
            return ['status' => 'not_found'];
        }

        return $this->command('/ppp/secret/disable', [".id={$id}"]);
    }

    public function setQueue(string $name, int $download, int $upload): array
    {
        $queueId = $this->findQueueId($name);

        $params = [
            "name={$name}",
            "max-limit={$upload}/{$download}",
            "queue=pcq-upload-default/pcq-download-default",
        ];

        if ($queueId) {
            return $this->command('/queue/simple/set', array_merge([".id={$queueId}"], $params));
        }

        return $this->command('/queue/simple/add', array_merge([
            "target={$name}",
        ], $params));
    }

    public function getQueue(string $name): array
    {
        $result = $this->command('/queue/simple/print', ["?name={$name}"]);
        return $result[0] ?? [];
    }

    public function getResourceUsage(): array
    {
        $result = $this->command('/system/resource/print');
        return $result[0] ?? [];
    }

    public function syncPppoeSecrets(Server $server): array
    {
        if (!$this->connect($server)) {
            return ['status' => 'error', 'message' => 'Connection failed'];
        }

        $links = Link::whereHas('contract', fn($q) => $q->where('status', 'active'))
            ->with(['contract.client', 'contract.plan'])
            ->get();

        $synced = 0;
        $errors = 0;

        foreach ($links as $link) {
            $username = $link->pppoe_user ?? "cli-{$link->contract->client_id}-{$link->id}";
            $password = $link->pppoe_password ?? $link->contract->client->cpf_cnpj ?? '123456';

            try {
                if ($link->contract->status === 'active') {
                    $result = $this->addPppoeUser($username, $password);
                    $synced++;
                } else {
                    $this->removePppoeUser($username);
                }
            } catch (\Exception $e) {
                Log::error("RouterOS sync error for {$username}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->disconnect();

        return [
            'status' => 'ok',
            'synced' => $synced,
            'errors' => $errors,
        ];
    }

    public function applyBandwidthProfiles(Server $server): array
    {
        if (!$this->connect($server)) {
            return ['status' => 'error', 'message' => 'Connection failed'];
        }

        $links = Link::whereHas('contract', fn($q) => $q->where('status', 'active'))
            ->with('contract.plan')
            ->get();

        $applied = 0;
        $errors = 0;

        foreach ($links as $link) {
            $plan = $link->contract->plan;
            if (!$plan) {
                continue;
            }

            $username = $link->pppoe_user ?? "cli-{$link->contract->client_id}-{$link->id}";
            $download = ($plan->bandwidth_download ?? 100) * 1000;
            $upload = ($plan->bandwidth_upload ?? 50) * 1000;

            try {
                $this->setQueue($username, $download, $upload);
                $applied++;
            } catch (\Exception $e) {
                Log::error("RouterOS bandwidth error for {$username}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->disconnect();

        return [
            'status' => 'ok',
            'applied' => $applied,
            'errors' => $errors,
        ];
    }

    protected function findPppoeUserId(string $username): ?string
    {
        $result = $this->command('/ppp/secret/print', ["?name={$username}"]);
        return $result[0]['.id'] ?? null;
    }

    protected function findQueueId(string $name): ?string
    {
        $result = $this->command('/queue/simple/print', ["?name={$name}"]);
        return $result[0]['.id'] ?? null;
    }

    protected function command(string $command, array $params = []): array
    {
        $this->write($command);
        foreach ($params as $param) {
            $this->write($param);
        }
        $this->write('');

        return $this->read();
    }

    protected function write(string $data): void
    {
        if (!$this->socket) {
            throw new \RuntimeException('Socket not connected');
        }
        $len = strlen($data);
        $this->writeLength($len);
        socket_write($this->socket, $data, $len);
    }

    protected function writeLength(int $length): void
    {
        if ($length < 0x80) {
            socket_write($this->socket, chr($length), 1);
        } elseif ($length < 0x4000) {
            $length |= 0x8000;
            socket_write($this->socket, chr(($length >> 8) & 0xff) . chr($length & 0xff), 2);
        } elseif ($length < 0x200000) {
            $length |= 0xC00000;
            socket_write($this->socket, chr(($length >> 16) & 0xff) . chr(($length >> 8) & 0xff) . chr($length & 0xff), 3);
        } elseif ($length < 0x10000000) {
            $length |= 0xE0000000;
            socket_write($this->socket, chr(($length >> 24) & 0xff) . chr(($length >> 16) & 0xff) . chr(($length >> 8) & 0xff) . chr($length & 0xff), 4);
        } else {
            socket_write($this->socket, chr(0xF0) . chr(($length >> 24) & 0xff) . chr(($length >> 16) & 0xff) . chr(($length >> 8) & 0xff) . chr($length & 0xff), 5);
        }
    }

    protected function read(): array
    {
        $result = [];
        while (true) {
            $length = $this->readLength();
            if ($length === null) {
                break;
            }
            if ($length === 0) {
                continue;
            }
            $data = socket_read($this->socket, $length);
            if ($data === false || $data === '') {
                break;
            }
            $result[] = $data;
        }
        return $result;
    }

    protected function readLength(): ?int
    {
        $byte = @socket_read($this->socket, 1);
        if ($byte === false || $byte === '' || $byte === null) {
            return null;
        }

        $ord = ord($byte);

        if ($ord < 0x80) {
            return $ord;
        }
        if ($ord < 0xC0) {
            $byte2 = @socket_read($this->socket, 1);
            return (($ord & 0x3F) << 8) + ord($byte2);
        }
        if ($ord < 0xE0) {
            $byte2 = @socket_read($this->socket, 1);
            $byte3 = @socket_read($this->socket, 1);
            return (($ord & 0x1F) << 16) + (ord($byte2) << 8) + ord($byte3);
        }
        if ($ord < 0xF0) {
            $byte2 = @socket_read($this->socket, 1);
            $byte3 = @socket_read($this->socket, 1);
            $byte4 = @socket_read($this->socket, 1);
            return (($ord & 0x0F) << 24) + (ord($byte2) << 16) + (ord($byte3) << 8) + ord($byte4);
        }

        $byte2 = @socket_read($this->socket, 1);
        $byte3 = @socket_read($this->socket, 1);
        $byte4 = @socket_read($this->socket, 1);
        $byte5 = @socket_read($this->socket, 1);
        return (ord($byte2) << 24) + (ord($byte3) << 16) + (ord($byte4) << 8) + ord($byte5);
    }
}
