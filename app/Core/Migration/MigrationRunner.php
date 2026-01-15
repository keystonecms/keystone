<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;
use Psr\Log\LoggerInterface;

final class MigrationRunner {
    public function __construct(
        private PDO $pdo,
        private MigrationRepository $repository,
        private LoggerInterface $logger,
        private array $executed = []
    ) {}

    public function executed(): array {
        return $this->executed;
    }


    public function run(array $migrations): void {
        foreach ($migrations as $migration) {

            $plugin  = $migration->getPlugin();
            $version = $migration->getVersion();

            if ($this->repository->hasRun($plugin, $version)) {
                continue;
            }

            $this->logger->info('Running migration', [
                'plugin' => $plugin,
                'version' => $version,
            ]);

            $migration->up($this->pdo);
            
            $this->executed[] = get_class($migration);
            
            $this->repository->markAsRun($plugin, $version);
        }
    }
}
