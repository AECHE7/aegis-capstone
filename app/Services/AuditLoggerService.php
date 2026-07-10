<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuthLog;
use App\Models\AdminActionLog;
use App\Models\ConfigChangeLog;
use App\Models\ExportAccessLog;
use App\Models\User;

class AuditLoggerService
{
    /**
     * Log authentication events.
     *
     * @param int|User|null $user
     * @param string $emailAttempted
     * @param string $eventType
     * @param string $ipAddress
     * @param string $userAgent
     * @param string $status
     * @param array $metadata
     * @return AuthLog
     */
    public static function logAuth(
        $user,
        string $emailAttempted,
        string $eventType,
        string $ipAddress,
        string $userAgent,
        string $status,
        array $metadata = []
    ): AuthLog {
        $userId = $user instanceof User ? $user->id : $user;

        return AuthLog::create([
            'user_id' => $userId,
            'email_attempted' => $emailAttempted,
            'event_type' => $eventType,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => $status,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log admin actions.
     *
     * @param int|User $user
     * @param string $action
     * @param string $targetType
     * @param string|int|null $targetId
     * @param string $description
     * @param string|null $ipAddress
     * @return AdminActionLog
     */
    public static function logAdminAction(
        $user,
        string $action,
        string $targetType,
        $targetId,
        string $description,
        ?string $ipAddress = null
    ): AdminActionLog {
        $userId = $user instanceof User ? $user->id : $user;

        return AdminActionLog::create([
            'user_id' => $userId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId !== null ? (string) $targetId : null,
            'description' => $description,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Log configuration/settings changes.
     *
     * @param int|User $user
     * @param string $settingKey
     * @param string|null $oldValue
     * @param string|null $newValue
     * @param string $ipAddress
     * @return ConfigChangeLog
     */
    public static function logConfigChange(
        $user,
        string $settingKey,
        ?string $oldValue,
        ?string $newValue,
        string $ipAddress
    ): ConfigChangeLog {
        $userId = $user instanceof User ? $user->id : $user;

        return ConfigChangeLog::create([
            'user_id' => $userId,
            'setting_key' => $settingKey,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Log export access attempts.
     *
     * @param int|User $user
     * @param string $exportType
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param string $format
     * @param string $ipAddress
     * @return ExportAccessLog
     */
    public static function logExportAccess(
        $user,
        string $exportType,
        ?string $dateFrom,
        ?string $dateTo,
        string $format,
        string $ipAddress
    ): ExportAccessLog {
        $userId = $user instanceof User ? $user->id : $user;

        return ExportAccessLog::create([
            'user_id' => $userId,
            'export_type' => $exportType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'format' => $format,
            'ip_address' => $ipAddress,
        ]);
    }
}
