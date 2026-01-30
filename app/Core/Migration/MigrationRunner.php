<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;
use Psr\Log\LoggerInterface;
use Keystone\Core\Migration\MigrationRepository;

final class MigrationRunner {

    private array $executed = [];

    public function __construct(
        private MigrationRepository $repository,
        private LoggerInterface $logger
    ) {}

    public function executed(): array {
        return $this->executed;
    }

    public function run(PDO $pdo, array $migrations): void {
    foreach ($migrations as $migration) {
        $plugin  = $migration->getPlugin();
        $version = $migration->getVersion();


        try {
            if ($this->repository->hasRun($pdo, $plugin, $version)) {
                continue;
            }
        } catch (\PDOException $e) {
            // migrations table bestaat nog niet → eerste migration
        }

        $this->logger->info('Running migration', [
            'plugin'  => $plugin,
            'version' => $version,
        ]);

        $migration->up($pdo);

        // markeer pas NA aanmaken van migrations-table
        try {
            $this->repository->markAsRun($pdo, $plugin, $version);
        } catch (\PDOException $e) {
            // table bestaat nog niet → ok
        }
    }
}

}

?>