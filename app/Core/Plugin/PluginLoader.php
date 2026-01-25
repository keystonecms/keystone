<?php

namespace Keystone\Core\Plugin;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;

use Keystone\Core\Migration\MigrationInterface;
use Keystone\Core\Migration\MigrationRunner;
use RuntimeException;
use Keystone\Core\Plugin\PluginRegistryInterface;

final class PluginLoader {

    public function __construct(
        private ContainerInterface $container,
        private LoggerInterface $logger,
        private PluginRegistryInterface $pluginRepository
    ) {}

    /**
     * @param PluginDescriptor[] $descriptors
     */
    public function load(App $app, array $descriptors): void
    {

    usort($descriptors, fn ($a, $b) =>
            $a->loadOrder <=> $b->loadOrder
    );


        foreach ($descriptors as $descriptor) {

$this->logger->debug('Plugin enabled check', [
    'slug' => $descriptor->slug,
    'enabled' => $this->pluginRepository->isEnabled($descriptor->slug),
]);

            // alleen enabled plugins laden
            if (!$this->pluginRepository->isEnabled($descriptor->slug)) {
                continue;
            }

            $this->logger->info('Loading plugin', [
                'plugin' => $descriptor->name,
            ]);

            $pluginClass = $descriptor->class;
            $plugin = new $pluginClass();

            $plugin->register($this->container);
            $plugin->boot($app, $this->container);

            $this->loadAndRunMigrations(
                $descriptor->name,
                $descriptor->path
            );

            $this->logger->info('Plugin loaded', [
                'plugin' => $descriptor->name,
                'version' => $descriptor->version,
            ]);
        }
    }

/**
 * @param string $pluginName
 * @param string $pluginPath
 */
private function loadAndRunMigrations(
    string $pluginName,
    string $pluginPath
): void {
    $migrationDir = $pluginPath . '/migrations';

    if (!is_dir($migrationDir)) {
        return;
    }

    $migrations = [];

    foreach (glob($migrationDir . '/*.php') as $file) {
        $migration = require $file;

        if (!$migration instanceof MigrationInterface) {
            throw new RuntimeException(
                "Invalid migration {$file} in plugin {$pluginName}"
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