<?php

namespace Keystone\Core\Security;

use PDO;

final class SecurityEventRepository {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function insert(
        int $userId,
        string $type,
        string $ip
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO security_events (user_id, type, ip_address)
             VALUES (:user, :type, :ip)'
        );

        $stmt->execute([
            'user' => $userId,
            'type' => $type,
            'ip'   => $ip,
        ]);
    }

    public function hasSeenIpBefore(
        int $userId,
        string $ip
    ): bool {
        $stmt = $this->pdo->prepare(
            'SELECT 1
               FROM security_events
              WHERE user_id = :user
                AND type = "login_success"
                AND ip_address = :ip
              LIMIT 1'
        );

        $stmt->execute([
            'user' => $userId,
            'ip'   => $ip,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function failedAttemptsLastMinutes(
        int $userId,
        int $minutes
    ): int {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
               FROM security_events
              WHERE user_id = :user
                AND type = "login_failed"
                AND occurred_at >= (NOW() - INTERVAL :minutes MINUTE)'
        );

        $stmt->execute([
            'user'    => $userId,
            'minutes' => $minutes,
        ]);

        return (int) $stmt->fetchColumn();
    }
}


?>