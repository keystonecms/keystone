<?php

declare(strict_types=1);

namespace Keystone\Core\Plugin;

interface PluginRegistryInterface {
    
    public function exists(string $slug): bool;

    public function register(
        string $slug,
        string $package,
        string $name,
        string $version,
        bool $enabled = false
    ): void;

    public function allIndexedByPackage(): array;

    public function enable(string $slug): void;

    public function count(): int;

    public function disable(string $slug): void;

    public function isEnabled(string $slug): bool;

    public function install(PluginDescriptor $plugin): void;

    public function updateVersion(string $slug, string $version): void;

    public function existsByPackage(string $package): bool;

    public function removeByPackage(string $package): void;

    public function getByPackage(string $package): ?array;

    public function get(string $slug): array;

    public function all(): array;

    public function allIndexedBySlug(): array;
}

?>