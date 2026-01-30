<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateMigrationsTable {
    public function getPlugin(): string {
        return 'core';
    }

    public function getVersion(): string {
        return '0000_create_migrations_table';
    }

    public function up(PDO $pdo): void {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                plugin VARCHAR(100) NOT NULL,
                version VARCHAR(50) NOT NULL,
                executed_at DATETIME NOT NULL,
                PRIMARY KEY (plugin, version)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }
}

?>
