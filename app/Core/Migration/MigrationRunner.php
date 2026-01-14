<?php

declare(strict_types=1);

namespace Keystone\Core\Migration;

use PDO;
use Psr\Log\LoggerInterface;

final class MigrationRunner {
    public function __construct(
        private PDO $pdo,
        private MigrationRepository $repository,
        private LoggerInterface $logger
    ) {}

    /**
     * @param MigrationInterface[] $migrations
     */
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

            $this->repository->markAsRun($plugin, $version);
        }
    }
}
