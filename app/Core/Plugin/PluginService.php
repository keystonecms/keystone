<?php

namespace Keystone\Core\Plugin;

final class PluginService {
    public function __construct(
        private PluginDiscovery $discovery,
        private PluginRepositoryInterface $repository,
        private PluginSyncService $sync
    ) {}

public function listPlugins(): array
{
    $discovered = $this->discovery->discover();

    // Zorgt dat nieuwe plugins in DB komen
    $this->sync->sync($discovered);

    // Indexed by package (Composer truth)
    $installed = [];
    foreach ($this->repository->all() as $entity) {
        $installed[$entity->getPackage()] = $entity;
    }

    $result = [];

    foreach ($discovered as $plugin) {
        $db = $installed[$plugin->package] ?? null;

        $result[] = [
            'name'        => $plugin->name,
            'package'     => $plugin->package,
            'version'     => $plugin->version,
            'description' => $plugin->description,
            'installed'   => $db !== null,
            'enabled'     => $db ? $db->isEnabled() : false,
        ];
    }

    return $result;
}

public function enable(string $name): void {
        $this->repository->enable($name);
    }

    public function disable(string $name): void {
        $this->repository->disable($name);
    }
}

?>