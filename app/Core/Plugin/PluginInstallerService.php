<?php

namespace Keystone\Core\Plugin;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Keystone\Core\Plugin\ComposerRunner;
use Keystone\Core\Plugin\PluginRegistryInterface;

final class PluginInstallerService {

    public function __construct(
        private PluginRegistryInterface $registry,
        private ComposerRunner $composer
    ) {}

public function install(string $package): void {

       if (!str_starts_with($package, 'keystone/plugin')) {
        throw new RuntimeException(
            "Invalid package name [$package]. Expected vendor/package."
        );
    }

        $this->composer->assertAvailable();

        if ($this->registry->existsByPackage($package)) {
            throw new RuntimeException(
                "Plugin [$package] is already installed."
            );
        }

        $this->composer->require($package);
    }

    public function update(string $package): void {
        $this->composer->assertAvailable();
        $this->composer->update($package);
    }

    public function remove(string $package): void {
        $this->composer->assertAvailable();
        $this->composer->remove($package);

        $this->registry->removeByPackage($package);
    }
}

?>