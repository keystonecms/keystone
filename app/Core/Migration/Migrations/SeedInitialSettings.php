<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;


final class SeedInitialSettings  {


    public function getPlugin(): string {
        return 'core';
    }

   public function getVersion(): string {
        return '0020_seed_initial_settings';
        }

    public function up(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO settings (`key`, `value`)
             VALUES (:key, :value)'
        );

        $stmt->execute([
            'key'   => 'theme.active',
            'value' => 'default',
        ]);
    }
}

?>