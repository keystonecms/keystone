<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateMenuTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0016_create_menu_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE menus (
            id int(11) NOT NULL,
            name varchar(100) NOT NULL,
            handle varchar(100) NOT NULL,
            description varchar(255) DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE menus
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY uniq_menus_handle (handle);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE menus
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);
            }
    }
?>