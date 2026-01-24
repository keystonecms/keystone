<?php

namespace Keystone\Core\Plugin;

interface PluginRepositoryInterface {

    public function all(): array;

    public function find(string $name): ?array;

    public function install(PluginDescriptor $plugin): void;

    public function enable(string $name): void;

    public function disable(string $name): void;

    public function isEnabled(string $name): bool;
}


?>