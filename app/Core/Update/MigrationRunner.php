<?php

declare(strict_types=1);

namespace Keystone\Core\Update;

use PDO;

final class MigrationRunner
{
    public function __construct(
        private PDO $pdo,
        private string $migrationPath
    ) {}

    public function run(): void
    {
        foreach (glob($this->migrationPath . '/*.php') as $file) {
            $migration = require $file;
            $this->pdo->exec($migration['up']);
        }
    }
}
