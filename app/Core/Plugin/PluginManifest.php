<?php

namespace Keystone\Core\Plugin;

final class PluginManifest {
 
   public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $version,
        public readonly string $keystone,
        public readonly bool $migrations,
        public readonly bool $assets,
        public readonly string $entry
    ) {}

    public function hasMigrations(): bool
    {
        return $this->migrations;
    }

    public function hasAssets(): bool
    {
        return $this->assets;
    }
}

?>