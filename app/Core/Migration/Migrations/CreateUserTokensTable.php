<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateUserTokensTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0013_create_user_tokens_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
                CREATE TABLE user_tokens (
                id int(10) UNSIGNED NOT NULL,
                user_id int(10) UNSIGNED NOT NULL,
                type enum('activation','password_reset','two_factor') NOT NULL,
                token_hash char(64) NOT NULL,
                expires_at datetime NOT NULL,
                used_at datetime DEFAULT NULL,
                created_at datetime NOT NULL DEFAULT current_timestamp()
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_tokens
                ADD PRIMARY KEY (id),
                ADD UNIQUE KEY uniq_user_tokens_hash (token_hash),
                ADD KEY idx_user_tokens_user_type (user_id,type),
                ADD KEY idx_user_tokens_expires (expires_at);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_tokens
                MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_tokens
                ADD CONSTRAINT fk_user_tokens_user FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;
        SQL;

        $pdo->exec($sql);
            }
    }
?>
