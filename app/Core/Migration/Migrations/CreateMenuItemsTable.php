<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreateMenuItemsTable {

    public function getPlugin(): string {
        // "core" voor core migrations
        // plugins kunnen hier hun eigen slug gebruiken
        return 'core';
    }

    public function getVersion(): string {
        // uniek per plugin
        // string is bewust (geen int)
        return '0018_create_menu_items_table';
    }

    public function up(PDO $pdo): void {
        $sql = <<<SQL
                CREATE TABLE menu_items (
                id int(11) NOT NULL,
                menu_id int(11) NOT NULL,
                parent_id int(11) DEFAULT NULL,
                label varchar(255) NOT NULL,
                link_type varchar(20) NOT NULL,
                link_target varchar(255) NOT NULL,
                sort_order int(11) NOT NULL DEFAULT 0,
                is_visible tinyint(1) NOT NULL DEFAULT 1,
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                css_class varchar(255) DEFAULT NULL,
                link_target_attr varchar(20) DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE menu_items
            ADD PRIMARY KEY (id),
            ADD KEY idx_menu_items_menu (menu_id),
            ADD KEY idx_menu_items_parent (parent_id);
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE menu_items
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;
        SQL;

        $pdo->exec($sql);

        $sql = <<<SQL
            ALTER TABLE menu_items
            ADD CONSTRAINT fk_menu_items_menu FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE,
            ADD CONSTRAINT fk_menu_items_parent FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON DELETE CASCADE;
         SQL;

        $pdo->exec($sql);
            }
    }
?>
