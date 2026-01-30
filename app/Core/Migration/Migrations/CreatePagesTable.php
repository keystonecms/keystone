<?php

declare(strict_types=1);

namespace Keystone\Core\Migration\Migrations;

use PDO;

final class CreatePagesTable {

    public function getPlugin(): string {
        return 'core';
    }

    public function getVersion(): string {
        return '0001_create_pages_table';
    }


    public function up(PDO $pdo): void {
        $sql = <<<SQL
            CREATE TABLE pages (
            id int(10) UNSIGNED NOT NULL,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            content_mode enum('richtext','blocks') NOT NULL DEFAULT 'richtext',
            content_html mediumtext DEFAULT NULL,
            blocks longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(blocks)),
            template varchar(100) DEFAULT NULL,
            status enum('draft','published','archived') NOT NULL DEFAULT 'draft',
            is_homepage tinyint(1) NOT NULL DEFAULT 0,
            published_version_id int(10) UNSIGNED DEFAULT NULL,
            next_publication datetime DEFAULT NULL,
            author_id int(10) UNSIGNED NOT NULL,
            created_at datetime NOT NULL DEFAULT current_timestamp(),
            updated_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
            SQL;

        $pdo->exec($sql);
    }
}
