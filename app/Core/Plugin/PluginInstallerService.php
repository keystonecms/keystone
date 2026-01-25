<?php

namespace Keystone\Core\Plugin;

use Psr\Container\ContainerInterface;
use RuntimeException;

use Keystone\Core\Plugin\PluginDownloader;
use Keystone\Core\Plugin\PluginFilesystem;
use Keystone\Core\Plugin\PluginRegistry;
use Keystone\Core\Plugin\Validation\PluginEntryValidator;


final class PluginInstallerService {
    public function __construct(
        private PluginDownloader $downloader,
        private PluginFilesystem $filesystem,
        private PluginRegistry $registry,
        private ContainerInterface $container,
        private PluginEntryValidator $validator
    ) {}

public function install(string $slug): void {


    if ($this->registry->exists($slug)) {
        throw new RuntimeException("Plugin [$slug] is already installed.");
    }

    // Download
    $archive = $this->downloader->download($slug);

    // Extract
    $pluginPath = $this->filesystem->extract($archive, $slug);

    $pluginName = ucfirst($slug);

    // Namespace / entry validation
    $this->validator->validate($pluginName, $pluginPath);

    // Manifest
    $manifest = $this->filesystem->readManifest($pluginPath);

    // Compatibility
    $this->assertCompatible($manifest);

    // Load plugin
    $plugin = $this->filesystem->loadPluginClass($manifest);

    // Plugin install hook
    if (method_exists($plugin, 'install')) {
        $plugin->install($this->container);
    }

    // Migrations
    if ($manifest->hasMigrations()) {
        $this->filesystem->runMigrations($pluginPath);
    }

    // Assets
    if ($manifest->hasAssets()) {
        $this->filesystem->publishAssets($pluginPath, $manifest->slug);
    }

    // Register LAST
    $this->registry->register(
        slug: $manifest->slug,
        version: $manifest->version,
        enabled: false
    );
}



public function update(string $slug): void
{
    if (!$this->registry->exists($slug)) {
        throw new RuntimeException("Plugin [$slug] is not installed.");
    }

    $installed = $this->registry->get($slug);

    // step 1:  Fetch latest manifest (remote)
    $latest = $this->downloader->fetchLatestManifest($slug);

    // step 2: Version check
    if (version_compare($latest->version, $installed->version, '<=')) {
        throw new RuntimeException(
            "Plugin [$slug] is already up to date."
        );
    }

    // step 3: Download new version
    $archive = $this->downloader->download($slug);

    // step 4: Backup current plugin
    $this->filesystem->backup($slug);

    // step 5: Replace files
    $pluginPath = $this->filesystem->extract($archive, $slug);

    $pluginName = ucfirst($slug);

    // step 6: Validate new code
    $this->validator->validate($pluginName, $pluginPath);

    // step 7: Compatibility
    $this->assertCompatible($latest);

    // step 8: Load plugin
    $plugin = $this->filesystem->loadPluginClass($latest);

    // step 9: Plugin update hook
    if (method_exists($plugin, 'update')) {
        $plugin->update(
            $installed->version,
            $latest->version,
            $this->container
        );
    }

    // step 10: Migrations
    if ($latest->hasMigrations()) {
        $this->filesystem->runMigrations($pluginPath);
    }

    // step 11: Assets
    if ($latest->hasAssets()) {
        $this->filesystem->publishAssets($pluginPath, $latest->slug);
    }

    // step 12: Update registry LAST
    $this->registry->updateVersion(
        $slug,
        $latest->version
    );
}


private function assertCompatible(PluginManifest $manifest): void {
    if (!version_compare(
        KEYSYSTEM_VERSION,
        $manifest->keystone,
        '>='
    )) {
        throw new RuntimeException(
            "Plugin {$manifest->name} is not compatible with this Keystone version."
        );
    }
}



}

?>
