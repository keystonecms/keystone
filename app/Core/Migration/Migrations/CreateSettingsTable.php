<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateSettingsTable {

    public function getPlugin(): string {
        return 'core';
    }

    public function getVersion(): string {
        return '0009_create_settings_table';
    }

    public function up(PDO $pdo): void {

            $sql = <<<SQL
                CREATE TABLE settings (
                `key` varchar(190) NOT NULL,
                value text NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
           SQL;

        $pdo->exec($sql);

        $sql = "";

            $sql = <<<SQL
                ALTER TABLE settings
                ADD PRIMARY KEY (`key`);
            SQL;

        $pdo->exec($sql);
        }
}

?>