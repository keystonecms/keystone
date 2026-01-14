<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;

final class MigrationRepository {


    public function __construct(
        private PDO $pdo
    ) {}

    public function hasRun(string $plugin, string $version): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM migrations WHERE plugin = :plugin AND version = :version'
        );
        $stmt->execute([
            'plugin' => $plugin,
            'version' => $version,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function markAsRun(string $plugin, string $version): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO migrations (plugin, version, executed_at)
             VALUES (:plugin, :version, NOW())'
        );

        $stmt->execute([
            'plugin' => $plugin,
            'version' => $version,
        ]);
    }
}

?>