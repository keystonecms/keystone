<?php

declare(strict_types=1);

namespace Keystone\Tests\Integration\Setup;

use Keystone\Core\Setup\SetupConfig;
use Keystone\Infrastructure\Database\MigrationRunner;
use PDO;
use PHPUnit\Framework\TestCase;

final class MigrationRunnerTest extends TestCase {
    private PDO $pdo;
    private MigrationRunner $runner;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $config = new SetupConfig(
            envPath: '/tmp/.env',
            lockFilePath: '/tmp/installed.lock',
            migrationPath: __DIR__ . '/../../Fixtures/migrations'
        );

        $this->runner = new MigrationRunner(
            $this->pdo,
            $config
        );
    }

    public function test_run_fresh_creates_all_tables(): void
    {
        $this->runner->runFresh();

        $tables = $this->pdo
            ->query("SELECT name FROM sqlite_master WHERE type='table'")
            ->fetchAll(PDO::FETCH_COLUMN);

        $this->assertContains('users', $tables);
        $this->assertContains('schema_migrations', $tables);
    }

    public function test_run_pending_is_idempotent(): void
    {
        // First run
        $this->runner->runPending();

        $count1 = $this->pdo
            ->query('SELECT COUNT(*) FROM schema_migrations')
            ->fetchColumn();

        // Second run (should do nothing)
        $this->runner->runPending();

        $count2 = $this->pdo
            ->query('SELECT COUNT(*) FROM schema_migrations')
            ->fetchColumn();

        $this->assertSame($count1, $count2);
    }

    public function test_run_pending_only_runs_new_migrations(): void
    {
        // Run first migration only
        $this->runner->runPending();

        // Simulate adding a new migration
        file_put_contents(
            __DIR__ . '/../../Fixtures/migrations/003_add_dummy.sql',
            'ALTER TABLE users ADD COLUMN dummy TEXT;'
        );

        $this->runner->runPending();

        $columns = $this->pdo
            ->query("PRAGMA table_info(users)")
            ->fetchAll(PDO::FETCH_COLUMN, 1);

        $this->assertContains('dummy', $columns);
    }
}

?>