#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Keystone\Core\Update\MigrationRunner;

// .env laden
Dotenv::createImmutable(dirname(__DIR__))->load();

// PDO maken
$pdo = new PDO(
    $_ENV['DB_DSN'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]
);

// Migraties draaien
$runner = new MigrationRunner(
    $pdo,
    __DIR__ . '/../database/migrations'
);

$runner->run();

echo "✔ Database migrations uitgevoerd\n";
?>