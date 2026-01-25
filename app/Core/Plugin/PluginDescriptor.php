<?php

namespace Keystone\Core\Plugin;

final class PluginDescriptor {
    
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $package,
        public readonly string $version,
        public readonly string $description,
        public readonly string $class,
        public readonly string $path,
        public readonly int $loadOrder
    ) {}
}


?>