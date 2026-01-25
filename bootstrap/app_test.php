<?php

declare(strict_types=1);

define('APP_ENV', 'test');

// 🔑 Gebruik file-based SQLite
$_ENV['DB_DSN'] = 'sqlite:' . BASE_PATH . '/var/test.db';

// Zorg dat we schoon starten
@unlink(BASE_PATH . '/var/test.db');

$app = require BASE_PATH . '/bootstrap/app.php';


// Haal DE PDO uit de container
$pdo = $app->getContainer()->get(PDO::class);

// Run test migrations
$migrationsDir = BASE_PATH . '/tests/Fixtures/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $pdo->exec(file_get_contents($file));
}

return $app;

