<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreatePolicyTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0015_create_policy_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE policies (
            id int(10) UNSIGNED NOT NULL,
            key_name varchar(100) NOT NULL,
            label varchar(150) NOT NULL,
            category varchar(50) NOT NULL DEFAULT 'General'
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE policies
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY key_name (key_name);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE policies
            MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);
            }
    }
?>
