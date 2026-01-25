<?php
declare(strict_types=1);

namespace Keystone\Core\Plugin;

use Composer\Autoload\ClassLoader;
use RuntimeException;

final class PluginDiscovery {
    public function __construct(
        private ClassLoader $classLoader
    ) {}

    /**
     * @return PluginDescriptor[]
     */
    public function discover(): array {

        $loaderReflection = new \ReflectionClass($this->classLoader);
        $vendorDir = dirname($loaderReflection->getFileName(), 3);

       $lockFile = $vendorDir . '/composer.lock';

        if (!file_exists($lockFile)) {
            throw new RuntimeException('composer.lock not found');
        }

        $lock = json_decode(file_get_contents($lockFile), true);

        $packages = array_merge(
            $lock['packages'] ?? [],
            $lock['packages-dev'] ?? []
        );

        $plugins = [];

        foreach ($packages as $package) {

            if (($package['type'] ?? null) !== 'keystone-plugin') {
                continue;
            }

            if (
                !isset($package['extra']['keystone']['plugin-class']) ||
                !is_string($package['extra']['keystone']['plugin-class'])
            ) {
                throw new RuntimeException(
                    "Keystone plugin {$package['name']} missing plugin-class in composer.lock"
                );
            }

            $pluginClass = $package['extra']['keystone']['plugin-class'];

            if (!class_exists($pluginClass)) {
                throw new RuntimeException(
                    "Plugin class {$pluginClass} not autoloadable"
                );
            }

            $plugin = new $pluginClass();

            if (!$plugin instanceof PluginInterface) {
                throw new RuntimeException(
                    "{$pluginClass} must implement PluginInterface"
                );
            }

            $loaderReflection = new \ReflectionClass($this->classLoader);
            $vendorDir = dirname($loaderReflection->getFileName(), 2);

            $installed = require $vendorDir . '/composer/installed.php';

            $path = $installed['versions'][$package['name']]['install_path'] ?? null;

            if (!$path || !is_dir($path)) {
                throw new RuntimeException(
                    "Install path not found for {$package['name']}"
                );
            }
            $plugins[] = new PluginDescriptor(
                name: $plugin->getName(),
                package: $package['name'],
                version: $package['version'] ?? 'dev',
                description: $plugin->getDescription(),
                class: $pluginClass,
                path: $path,
                loadOrder: $plugin->getLoadOrder()
            );
        }

        return $plugins;
    }
}


?>