<?php
declare(strict_types=1);

namespace Keystone\Core\Plugin;

use Composer\Autoload\ClassLoader;
use RuntimeException;
use Keystone\Core\Plugin\PluginDiscoveryInterface;
use Keystone\Infrastructure\Paths;
use Keystone\Core\Plugin\Filesystem\PluginFilesystem;

final class PluginDiscovery implements PluginDiscoveryInterface {

    public function __construct(
        private ClassLoader $classLoader,
        private Paths $paths,
        private PluginFilesystem $filesystem
    ) {}

    /**
     * @return PluginDescriptor[]
     */
public function discover(): array {

    $pluginsPath = $this->paths->plugins();
    $descriptors = [];

foreach ($packages as $package) {
    if (($package['type'] ?? null) !== 'keystone-plugin') {
        continue;
    }

    $pluginClass = $package['extra']['keystone']['plugin-class'];

    if (!class_exists($pluginClass)) {
        throw new RuntimeException(
            "Plugin class {$pluginClass} not autoloadable"
        );
    }

    $plugin = new $pluginClass();

    $plugins[] = new PluginDescriptor(
        slug: $plugin->getName(),        // of expliciet slug
        name: $plugin->getName(),
        package: $package['name'],
        version: $package['version'],
        description: $plugin->getDescription(),
        class: $pluginClass,
        path: $path,
        loadOrder: $plugin->getLoadOrder()
    );
}


    return $descriptors;
}

}


?>