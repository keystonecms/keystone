<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateActivityLogTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0006_create_activity_log_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE activity_log (
            id bigint(20) UNSIGNED NOT NULL,
            message varchar(255) NOT NULL,
            actor_id bigint(20) UNSIGNED DEFAULT NULL,
            actor_type varchar(32) DEFAULT NULL,
            context varchar(64) DEFAULT NULL,
            context_id bigint(20) UNSIGNED DEFAULT NULL,
            occurred_at datetime NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE activity_log
            ADD PRIMARY KEY (id),
            ADD KEY idx_occurred_at (occurred_at),
            ADD KEY idx_actor (actor_type,actor_id),
            ADD KEY idx_context (context,context_id);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE activity_log
            MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
        SQL;

        $pdo->exec($sql);
            }
    }
?>


