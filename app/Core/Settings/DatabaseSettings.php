<?php

namespace Keystone\Core\Settings;

use PDO;

final class DatabaseSettings implements SettingsInterface {
    
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $stmt = $this->pdo->prepare(
            'SELECT value FROM settings WHERE `key` = :key'
        );
        $stmt->execute(['key' => $key]);

        return $stmt->fetchColumn() ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $stmt = $this->pdo->prepare(
            'REPLACE INTO settings (`key`, `value`) VALUES (:key, :value)'
        );
        $stmt->execute([
            'key' => $key,
            'value' => $value,
        ]);
    }
}


?>