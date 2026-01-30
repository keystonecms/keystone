<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateUserSecuritySettingsTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0017_create_user_security_settings_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
                CREATE TABLE user_security_settings (
                user_id int(10) UNSIGNED NOT NULL,
                notify_new_ip tinyint(1) NOT NULL DEFAULT 1,
                notify_failed_logins tinyint(1) NOT NULL DEFAULT 1
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_security_settings
                ADD PRIMARY KEY (user_id);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
                ALTER TABLE user_security_settings
                ADD CONSTRAINT fk_user_security_user FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;
        SQL;

        $pdo->exec($sql);
            }
    }
?>
