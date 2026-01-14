<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin;

use Keystone\Core\Migration\MigrationRunner;
use Keystone\Core\Migration\MigrationInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use RuntimeException;

final class PluginLoader {
    public function __construct(
        private ContainerInterface $container,
        private LoggerInterface $logger,
        private string $pluginPath
    ) {}

    public function load(App $app): void
    {
        foreach (glob($this->pluginPath . '/*/plugin.php') as $pluginFile) {

            $this->logger->info('Loading plugin', [
                'file' => $pluginFile,
            ]);

            $plugin = require $pluginFile;

            if (!$plugin instanceof PluginInterface) {
                throw new RuntimeException(
                    "Invalid plugin: {$pluginFile}"
                );
            }

            // 1️⃣ Services / DI
            $plugin->register($this->container);

            // 2️⃣ Routes / middleware
            $plugin->boot($app, $this->container);

            // 3️⃣ Migrations (DIT IS DE NIEUWE STAP)
            $this->loadAndRunMigrations(
                $plugin->getName(),
                dirname($pluginFile)
            );

            $this->logger->info('Plugin loaded', [
                'plugin' => $plugin->getName(),
            ]);
        }
    }

    /**
     * @return MigrationInterface[]
     */
    private function loadAndRunMigrations(
        string $pluginName,
        string $pluginDir
    ): void {

        $migrationDir = $pluginDir . '/migrations';

        if (!is_dir($migrationDir)) {
            return;
        }

        $migrations = [];

        foreach (glob($migrationDir . '/*.php') as $file) {
            $migration = require $file;

            if (!$migration instanceof MigrationInterface) {
                throw new RuntimeException(
                    "Invalid migration: {$file}"
                );
            }

            $migrations[] = $migration;
        }

        if ($migrations === []) {
            return;
        }

        $this->container
            ->get(MigrationRunner::class)
            ->run($migrations);
    }
}


?>