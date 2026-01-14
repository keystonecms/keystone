<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;

interface MigrationInterface {
    public function getPlugin(): string;

    public function getVersion(): string;

    public function up(PDO $pdo): void;
}
?>