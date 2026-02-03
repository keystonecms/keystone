<?php

/*
 * Keystone CMS
 *
 * @author Constan van Suchtelen van de Haere <constan.vansuchtelenvandehaere@hostingbe.com>
 * @copyright 2026 HostingBE
 * @package   Keystone CMS
 * @author    HostingBE
 * @license   MIT
 * @link      https://keystone-cms.com
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF
 * OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

declare(strict_types=1);

namespace Keystone\Core\Plugin;

use Composer\Autoload\ClassLoader;
use Keystone\Infrastructure\Paths;
use RuntimeException;

final class PluginDiscovery implements PluginDiscoveryInterface {


    public function __construct(
        private ClassLoader $classLoader,
        private Paths $paths,
    ) {}

    /**
     * @return PluginDescriptor[]
     */
    public function discover(): array {
        $plugins = array_merge(
            $this->discoverComposerPlugins(),
            $this->discoverFilesystemPlugins()
        );

        return $this->mergeAndDeduplicate($plugins);
    }

    /**
     * Composer-installed plugins
     *
     * @return PluginDescriptor[]
     */
    private function discoverComposerPlugins(): array {

    $projectRoot = $this->resolveProjectRoot();
    $lockFile    = $projectRoot . '/composer.lock';
    $installed   = require $projectRoot . '/vendor/composer/installed.php';


        if (!file_exists($lockFile)) {
            throw new RuntimeException(
                "composer.lock not found at {$lockFile}"
            );
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

            $pluginClass = $package['extra']['keystone']['plugin-class'] ?? null;
            $this->validatePluginClass($pluginClass, $package['name']);

            $path = $installed['versions'][$package['name']]['install_path'] ?? null;

            if (!$path || !is_dir($path)) {
                throw new RuntimeException("Install path not found for {$package['name']}");
            }

            $plugins[] = $this->createDescriptor(
                $pluginClass,
                $package['name'],
                $package['version'],
                $path
            );
        }

        return $plugins;
    }

    /**
     * Filesystem (dev) plugins
     *
     * @return PluginDescriptor[]
     */
    private function discoverFilesystemPlugins(): array
    {
        $plugins = [];

        foreach ($this->paths->pluginDevRoots() as $root) {
            foreach (glob($root . '/*/composer.json') as $composerFile) {

                $composer = json_decode(file_get_contents($composerFile), true);

                if (($composer['type'] ?? null) !== 'keystone-plugin') {
                    continue;
                }

                $pluginClass = $composer['extra']['keystone']['plugin-class'] ?? null;
                $this->validatePluginClass($pluginClass, $composer['name'] ?? 'dev-plugin');

                $plugins[] = $this->createDescriptor(
                    $pluginClass,
                    $composer['name'] ?? 'dev-plugin',
                    $composer['version'] ?? 'dev',
                    dirname($composerFile)
                );
            }
        }

        return $plugins;
    }

    private function createDescriptor(
        string $pluginClass,
        string $package,
        string $version,
        string $path
    ): PluginDescriptor {
        $plugin = new $pluginClass();

        if (!$plugin instanceof PluginInterface) {
            throw new RuntimeException("{$pluginClass} must implement PluginInterface");
        }

        return new PluginDescriptor(
            slug: $plugin->getName(),
            name: $plugin->getName(),
            package: $package,
            version: $version,
            description: $plugin->getDescription(),
            class: $pluginClass,
            path: $path,
            loadOrder: $plugin->getLoadOrder()
        );
    }

    private function validatePluginClass(?string $class, string $package): void
    {
        if (!$class || !is_string($class)) {
            throw new RuntimeException(
                "Keystone plugin {$package} missing plugin-class"
            );
        }

        if (!class_exists($class)) {
            throw new RuntimeException(
                "Plugin class {$class} not autoloadable"
            );
        }
    }

    /**
     * Dev plugins override Composer-installed ones
     */
    private function mergeAndDeduplicate(array $plugins): array
    {
        $byPackage = [];

        foreach ($plugins as $plugin) {
            $byPackage[$plugin->package] = $plugin;
        }

        return array_values($byPackage);
    }

 private function resolveProjectRoot(): string
{
    $reflection = new \ReflectionClass($this->classLoader);

    // vendor/composer/ClassLoader.php → project root
    return dirname($reflection->getFileName(), 3);
}
}

?>