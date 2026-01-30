<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateLoginAuditTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0013_create_login_audit_table';
    }

    public function up(PDO $pdo): void {
        
        $sql = <<<SQL
            CREATE TABLE login_audit (
            id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            ip varchar(45) NOT NULL,
            country varchar(2) DEFAULT NULL,
            region varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            timezone varchar(50) DEFAULT NULL,
            org varchar(150) DEFAULT NULL,
            created_at datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE login_audit
                ADD PRIMARY KEY (id),
                ADD KEY idx_user (user_id),
                ADD KEY idx_ip (ip);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE login_audit
                MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);


    }
}

?>