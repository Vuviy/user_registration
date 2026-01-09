<?php

namespace App\Services;

use App\DB\Database;

final class AuditLogger
{
    public static function log(?int $userId, string $action, string $status, array $metadata = []): void
    {
        $db = new Database(config());

        $db->table('audit_logs')->insert([
            'user_id' => $userId,
            'action' => $action,
            'status' => $status,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'metadata' => json_encode($metadata),
        ]);
    }
}