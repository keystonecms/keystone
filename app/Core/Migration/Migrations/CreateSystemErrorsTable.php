<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateSystemErrorsTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0011_create_system_errors_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE system_errors (
            id bigint(20) UNSIGNED NOT NULL,
            level varchar(20) NOT NULL,
            errorid varchar(32) DEFAULT NULL,
            message text NOT NULL,
            exception_class varchar(255) DEFAULT NULL,
            file varchar(255) DEFAULT NULL,
            line int(11) DEFAULT NULL,
            trace longtext DEFAULT NULL,
            request_uri varchar(255) DEFAULT NULL,
            method varchar(10) DEFAULT NULL,
            user_id int(10) UNSIGNED DEFAULT NULL,
            plugin varchar(100) DEFAULT NULL,
            resolved tinyint(1) NOT NULL DEFAULT 0,
            resolved_at datetime DEFAULT NULL,
            resolved_by int(10) UNSIGNED DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE system_errors
            ADD PRIMARY KEY (id),
            ADD KEY idx_level (level),
            ADD KEY idx_resolved (resolved),
            ADD KEY idx_created (created_at)
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE system_errors
            MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);


    }
}

?>