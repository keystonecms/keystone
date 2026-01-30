<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;

final class MigrationRepository {

    public function hasRun(PDO $pdo, string $plugin, string $version): bool {
        $stmt = $pdo->prepare(
            'SELECT 1
             FROM migrations
             WHERE plugin = :plugin
               AND version = :version
             LIMIT 1'
        );

        $stmt->execute([
            'plugin'  => $plugin,
            'version' => $version,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function markAsRun(PDO $pdo, string $plugin, string $version): void {
        $stmt = $pdo->prepare(
            'INSERT INTO migrations (plugin, version, executed_at)
             VALUES (:plugin, :version, NOW())'
        );

        $stmt->execute([
            'plugin'  => $plugin,
            'version' => $version,
        ]);
    }
}

?>