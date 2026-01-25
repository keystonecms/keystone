<?php

namespace Keystone\Core\Plugin;

interface PluginRegistryInterface {
    public function exists(string $slug): bool;
    public function register(string $slug, string $version): void;
    public function enable(string $slug): void;
    public function disable(string $slug): void;
    public function isEnabled(string $slug): bool;
    public function updateVersion(string $slug, string $version): void;
    public function all(): array;
}


?>