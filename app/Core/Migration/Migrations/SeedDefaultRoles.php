<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class SeedDefaultRoles {

    public function getPlugin(): string {
        return 'core';
    }

    public function getVersion(): string {
        return '0003_seed_default_roles';
    }

    public function up(PDO $pdo): void {

    $roles = [
            ['name' => 'admin',  'label' => 'Administrator'],
            ['name' => 'editor', 'label' => 'Editor'],
            ['name' => 'user',   'label' => 'User'],
        ];

        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO roles (name, label)
             VALUES (:name, :label)'
        );

        foreach ($roles as $role) {
            $stmt->execute($role);
        }
    }
}

?>