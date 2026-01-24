<?php

namespace Keystone\Core\User;

use PDO;

final class UserSecuritySettingsRepository {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function getForUser(int $userId): UserSecuritySettingsDto
    {
        $stmt = $this->pdo->prepare(
            'SELECT notify_new_ip, notify_failed_logins
               FROM user_security_settings
              WHERE user_id = :user'
        );

        $stmt->execute(['user' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // nog geen row? → defaults
        if (!$row) {
            return new UserSecuritySettingsDto(true, true);
        }

        return new UserSecuritySettingsDto(
            (bool) $row['notify_new_ip'],
            (bool) $row['notify_failed_logins'],
        );
    }

    public function saveForUser(
        int $userId,
        bool $notifyNewIp,
        bool $notifyFailedLogins
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_security_settings
                (user_id, notify_new_ip, notify_failed_logins)
             VALUES (:user, :newIp, :failed)
             ON DUPLICATE KEY UPDATE
                notify_new_ip = VALUES(notify_new_ip),
                notify_failed_logins = VALUES(notify_failed_logins)'
        );

        $stmt->execute([
            'user'   => $userId,
            'newIp'  => (int) $notifyNewIp,
            'failed' => (int) $notifyFailedLogins,
        ]);
    }
}

?>