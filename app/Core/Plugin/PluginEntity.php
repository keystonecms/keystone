<?php


namespace Keystone\Core\Plugin;

final class PluginEntity {
   
  public function __construct(
        private string $name,
        private string $package,
        private string $version,
        private bool $enabled,
        private int $loadOrder
    ) {}

    public function getPackage(): string
    {
        return $this->package;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }
}


?>