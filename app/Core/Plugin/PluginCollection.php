<?php


namespace Keystone\Core\Plugin;

use Keystone\Core\Plugin\PluginInterface;

final class PluginCollection
{
    private array $plugins = [];

    public function add(PluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = [
            'name'        => $plugin->getName(),
            'version'     => $plugin->getVersion(),
            'description' => $plugin->getDescription(),
        ];
    }

    public function all(): array {
        return $this->plugins;
    }
}


?>