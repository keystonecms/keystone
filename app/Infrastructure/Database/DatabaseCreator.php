<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Database;

use Keystone\Core\Setup\Database\DatabaseCreatorInterface;
use PDO;
use PDOException;
use RuntimeException;

final class DatabaseCreator implements DatabaseCreatorInterface {
    public function createIfNotExists(
        string $host,
        string $database,
        string $user,
        string $password,
        int $port = 3306
    ): void {
        try {
            $pdo = new PDO(
                "mysql:host={$host};port={$port}",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );

            $pdo->exec(
                sprintf(
                    'CREATE DATABASE IF NOT EXISTS `%s`
                     CHARACTER SET utf8mb4
                     COLLATE utf8mb4_unicode_ci',
                    $database
                )
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Database could not be created: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
?>