<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateRolesPolicyTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0017_create_roles_policy_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE role_policies (
            role_id int(10) UNSIGNED NOT NULL,
            policy_id int(10) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE role_policies
            ADD PRIMARY KEY (role_id,policy_id),
            ADD KEY policy_id (policy_id);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE role_policies
            ADD CONSTRAINT role_policies_ibfk_1 FOREIGN KEY (role_id) REFERENCES `roles` (id) ON DELETE CASCADE,
            ADD CONSTRAINT role_policies_ibfk_2 FOREIGN KEY (policy_id) REFERENCES policies (id) ON DELETE CASCADE;
        SQL;

        $pdo->exec($sql);
            }
    }
?>

