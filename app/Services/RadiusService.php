<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RadiusService
{
    protected string $connection;
    protected string $tablePrefix;

    public function __construct()
    {
        $this->connection = config('radius.connection', 'mysql');
        $this->tablePrefix = config('radius.table_prefix', 'rad');
    }

    public function addUser(string $username, string $password, string $group = 'users'): bool
    {
        try {
            $exists = DB::connection($this->connection)
                ->table("{$this->tablePrefix}check")
                ->where('username', $username)
                ->where('attribute', 'Cleartext-Password')
                ->exists();

            if (!$exists) {
                DB::connection($this->connection)
                    ->table("{$this->tablePrefix}check")
                    ->insert([
                        'username' => $username,
                        'attribute' => 'Cleartext-Password',
                        'op' => ':=',
                        'value' => $password,
                    ]);
            }

            $this->addGroup($username, $group);

            Log::info("RADIUS user added: {$username}");
            return true;
        } catch (\Exception $e) {
            Log::error("RADIUS add user error: {$e->getMessage()}");
            return false;
        }
    }

    public function removeUser(string $username): bool
    {
        try {
            DB::connection($this->connection)
                ->table("{$this->tablePrefix}check")
                ->where('username', $username)
                ->delete();

            DB::connection($this->connection)
                ->table("{$this->tablePrefix}usergroup")
                ->where('username', $username)
                ->delete();

            Log::info("RADIUS user removed: {$username}");
            return true;
        } catch (\Exception $e) {
            Log::error("RADIUS remove user error: {$e->getMessage()}");
            return false;
        }
    }

    public function enableUser(string $username): bool
    {
        return $this->setUserEnabled($username, true);
    }

    public function disableUser(string $username): bool
    {
        return $this->setUserEnabled($username, false);
    }

    public function addGroup(string $username, string $group): void
    {
        $exists = DB::connection($this->connection)
            ->table("{$this->tablePrefix}usergroup")
            ->where('username', $username)
            ->where('groupname', $group)
            ->exists();

        if (!$exists) {
            DB::connection($this->connection)
                ->table("{$this->tablePrefix}usergroup")
                ->insert([
                    'username' => $username,
                    'groupname' => $group,
                    'priority' => 1,
                ]);
        }
    }

    public function getAccounting(string $username): array
    {
        try {
            $acct = DB::connection($this->connection)
                ->table("{$this->tablePrefix}acct")
                ->where('username', $username)
                ->orderBy('acctstarttime', 'desc')
                ->first();

            return $acct ? (array) $acct : [];
        } catch (\Exception $e) {
            Log::error("RADIUS accounting error: {$e->getMessage()}");
            return [];
        }
    }

    public function syncUsers(): array
    {
        $links = Link::whereHas('contract', fn($q) => $q->where('status', 'active'))
            ->with('contract.plan')
            ->get();

        $synced = 0;
        $errors = 0;

        foreach ($links as $link) {
            try {
                $username = $link->pppoe_user ?? "cli-{$link->contract->client_id}-{$link->id}";
                $password = $link->pppoe_password ?? $link->contract->client->cpf_cnpj ?? '123456';
                $group = 'plan-' . ($link->contract->plan_id ?? 'default');

                $this->addUser($username, $password, $group);

                if ($link->contract->status !== 'active') {
                    $this->disableUser($username);
                } else {
                    $this->enableUser($username);
                }

                $synced++;
            } catch (\Exception $e) {
                Log::error("RADIUS sync error for link {$link->id}: {$e->getMessage()}");
                $errors++;
            }
        }

        return [
            'status' => 'ok',
            'synced' => $synced,
            'errors' => $errors,
        ];
    }

    protected function setUserEnabled(string $username, bool $enabled): bool
    {
        try {
            $attribute = 'Cleartext-Password';
            if (!$enabled) {
                $attribute = 'Auth-Type';
            }

            $check = DB::connection($this->connection)
                ->table("{$this->tablePrefix}check")
                ->where('username', $username);

            if ($enabled) {
                $check->where('attribute', 'Auth-Type')->delete();
            } else {
                $exists = $check->where('attribute', 'Auth-Type')->exists();
                if (!$exists) {
                    DB::connection($this->connection)
                        ->table("{$this->tablePrefix}check")
                        ->insert([
                            'username' => $username,
                            'attribute' => 'Auth-Type',
                            'op' => ':=',
                            'value' => 'Reject',
                        ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("RADIUS set user enabled error: {$e->getMessage()}");
            return false;
        }
    }
}
