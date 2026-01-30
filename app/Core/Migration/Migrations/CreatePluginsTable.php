<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreatePluginsTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0005_create_plugins_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE `plugins` (
            id int(10) UNSIGNED NOT NULL,
            slug varchar(100) NOT NULL,
            package varchar(150) NOT NULL,
            name varchar(150) NOT NULL,
            version varchar(50) NOT NULL,
            enabled tinyint(1) NOT NULL DEFAULT 0,
            load_order int(3) NOT NULL,
            installed_at datetime NOT NULL,
            updated_at datetime DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            CREATE TABLE plugin_migrations (
            id int(11) NOT NULL,
            plugin_name varchar(100) NOT NULL,
            migration varchar(255) NOT NULL,
            executed_at datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE plugins
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY uq_plugins_slug (slug),
            ADD UNIQUE KEY uq_plugins_package (package);
        SQL;

        $pdo->exec($sql);
        $sql = <<<SQL
            ALTER TABLE plugin_migrations
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY uniq_plugin_migration (plugin_name,migration);
        SQL;

     $pdo->exec($sql);
        $sql = <<<SQL
            ALTER TABLE plugins
            MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
        SQL;

         $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE plugin_migrations
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);
            }
    }
?>

