<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateSecurityEventsTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0012_create_security_events_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE security_events (
            id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            type varchar(64) NOT NULL,
            ip_address varchar(45) NOT NULL,
            occurred_at datetime NOT NULL DEFAULT current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE security_events
            ADD PRIMARY KEY (id),
            ADD KEY idx_user_type (user_id,type),
            ADD KEY idx_user_time (user_id,occurred_at);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE security_events
            MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);


    }
}

?>