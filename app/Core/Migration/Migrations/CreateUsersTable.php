<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateUsersTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0002_create_users_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
        CREATE TABLE users (
        id int(10) UNSIGNED NOT NULL,
        name varchar(64) NOT NULL,
        email varchar(191) NOT NULL,
        password_hash varchar(255) DEFAULT NULL,
        status enum('pending','active','disabled','') NOT NULL DEFAULT 'pending',
        email_verified_at timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        created_at datetime NOT NULL DEFAULT current_timestamp(),
        updated_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        two_factor_secret varchar(64) DEFAULT NULL,
        avatar_path varchar(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE users
                ADD PRIMARY KEY (id),
                ADD UNIQUE KEY uniq_users_email (email);
                SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE users
                MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
                COMMIT;
                SQL;

        $pdo->exec($sql);


    }
}

?>