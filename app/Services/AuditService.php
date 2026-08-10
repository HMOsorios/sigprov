<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditService
{
    public function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        string $description = '',
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        try {
            return AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => $userAgent ?? request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Audit log failed: ' . $e->getMessage());
            return new AuditLog();
        }
    }

    public function logCreate(string $entityType, int $entityId, string $description, ?array $newValues = null): AuditLog
    {
        return $this->log('create', $entityType, $entityId, $description, null, $newValues);
    }

    public function logUpdate(string $entityType, int $entityId, string $description, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return $this->log('update', $entityType, $entityId, $description, $oldValues, $newValues);
    }

    public function logDelete(string $entityType, int $entityId, string $description, ?array $oldValues = null): AuditLog
    {
        return $this->log('delete', $entityType, $entityId, $description, $oldValues, null);
    }

    public function logLogin(int $userId, string $description): AuditLog
    {
        return $this->log('login', 'user', $userId, $description);
    }

    public function logLogout(int $userId, string $description): AuditLog
    {
        return $this->log('logout', 'user', $userId, $description);
    }
}
