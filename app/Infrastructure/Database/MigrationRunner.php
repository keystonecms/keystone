<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Database;

use Keystone\Core\Setup\Database\MigrationRunnerInterface;
use Keystone\Core\Setup\SetupConfig;
use PDO;
use RuntimeException;
use Throwable;

final class MigrationRunner implements MigrationRunnerInterface {
    public function __construct(
        private PDO $pdo,
        private SetupConfig $config
    ) {}

    /**
     * Installer: drop everything and run all migrations
     */
    public function runFresh(): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->dropAllTables();
            $this->ensureMigrationsTable();
            $this->runAllMigrations();

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw new RuntimeException('Fresh migration failed', 0, $e);
        }
    }

    /**
     * Updater: only run migrations that were not executed yet
     */
    public function runPending(): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->ensureMigrationsTable();

            $executed = $this->getExecutedVersions();
            $files    = $this->getMigrationFiles();

            foreach ($files as $version => $file) {
                if (in_array($version, $executed, true)) {
                    continue;
                }

                $sql = file_get_contents($file);
                if ($sql === false) {
                    throw new RuntimeException("Cannot read migration: {$file}");
                }

                $this->pdo->exec($sql);
                $this->recordMigration($version);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw new RuntimeException('Pending migrations failed', 0, $e);
        }
    }

    /* ================================
       Internal helpers
       ================================ */

    private function ensureMigrationsTable(): void {
        $this->pdo->exec(
            <<<SQL
            CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) PRIMARY KEY,
                executed_at DATETIME NOT NULL
            )
            SQL
        );
    }

    private function getExecutedVersions(): array {
        $stmt = $this->pdo->query(
            'SELECT version FROM schema_migrations'
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return array<string, string> [version => filepath]
     */
    private function getMigrationFiles(): array
    {
        $path = rtrim($this->config->migrationPath, '/');

        if (!is_dir($path)) {
            throw new RuntimeException("Migration path not found: {$path}");
        }

        $files = glob($path . '/*.sql');
        sort($files);

        $migrations = [];

        foreach ($files as $file) {
            $version = basename($file, '.sql');
            $migrations[$version] = $file;
        }

        return $migrations;
    }

private function recordMigration(string $version): void
{
    $stmt = $this->pdo->prepare(
        'INSERT INTO schema_migrations (version, executed_at)
         VALUES (:version, :executed_at)'
    );

    $stmt->execute([
        'version'     => $version,
        'executed_at' => date('Y-m-d H:i:s'),
    ]);
}


private function dropAllTables(): void
{
    $driver = $this->getDriver();

    if ($driver === 'sqlite') {
        $tables = $this->pdo
            ->query("SELECT name FROM sqlite_master WHERE type='table'")
            ->fetchAll(PDO::FETCH_COLUMN);
    } else {
        // mysql / mariadb
        $tables = $this->pdo
            ->query('SHOW TABLES')
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    foreach ($tables as $table) {
        if ($table === 'schema_migrations') {
            continue; // laten we bestaan
        }

        $this->pdo->exec("DROP TABLE IF EXISTS `{$table}`");
    }
}


    private function runAllMigrations(): void
    {
        foreach ($this->getMigrationFiles() as $version => $file) {
            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException("Cannot read migration: {$file}");
            }

            $this->pdo->exec($sql);
            $this->recordMigration($version);
        }
    }
private function getDriver(): string
{
    return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
}


    }

?>