<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use PDO;
use PDOException;
use Keystone\Core\Setup\InstallerException;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Database\MigrationRunnerInterface;

final class MigrationStep extends AbstractInstallerStep {
    public function __construct(
        private MigrationRunnerInterface $migrationRunner
    ) {}

    public function getName(): string {
        return 'migration';
    }


    public function getTitle(): string {
    return 'Database Migration';
    }

    public function getDescription(): string {
        return 'We will import all de database tables and definitions which are needed to run Keystone CMS smoothly.';
        }


        public function shouldRun(InstallerState $state): bool {
            return $state->databaseValidated;
        }

    public function run(InstallerState $state): void {

    try {
            $pdo = new PDO(
                sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $state->dbHost === 'localhost' ? '127.0.0.1' : $state->dbHost,
                    $state->dbPort,
                    $state->dbName
                ),
                $state->dbUser,
                $state->dbPass ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        } catch (PDOException $e) {
            throw new InstallerException([
                'Could not connect to the database.',
                $e->getMessage(),
            ]);
        }

        try {
            $this->migrationRunner->runWithPdo($pdo);
        } catch (\Throwable $e) {
            throw new InstallerException([
                'Database migrations failed.',
                $e->getMessage(),
            ]);
        }
    }
}


?>
