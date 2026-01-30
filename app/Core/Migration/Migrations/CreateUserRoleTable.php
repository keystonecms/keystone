<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateUserRoleTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0004_create_user_role_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE user_roles (
            user_id int(10) UNSIGNED NOT NULL,
            role_id int(10) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_roles
                ADD PRIMARY KEY (user_id,role_id),
                ADD KEY role_id (role_id)
                SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                    ALTER TABLE user_roles
                    ADD CONSTRAINT user_roles_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                    ADD CONSTRAINT user_roles_ibfk_2 FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;
                    COMMIT
                SQL;

        $pdo->exec($sql);


    }
}

?>