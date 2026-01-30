<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateRolesTable {

    public function getPlugin(): string {
        return 'core';
    }

    public function getVersion(): string {
        return '0001_create_roles_table';
    }

    public function up(PDO $pdo): void {

            $sql = <<<SQL
                CREATE TABLE roles (
                id int(10) UNSIGNED NOT NULL,
                name varchar(50) NOT NULL,
                label varchar(100) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
           SQL;

        $pdo->exec($sql);

        $sql = "";

            $sql = <<<SQL
                ALTER TABLE roles
                ADD PRIMARY KEY (id),
                ADD UNIQUE KEY name (name);
            SQL;

        $pdo->exec($sql);

        $sql = "";

            $sql = <<<SQL
                ALTER TABLE roles
                MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
                COMMIT;
                SQL;
        $pdo->exec($sql);

    }
}

?>