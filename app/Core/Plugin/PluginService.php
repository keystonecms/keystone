<?php

namespace Keystone\Core\Plugin;

final class PluginService {
    public function __construct(
        private PluginDiscovery $discovery,
        private PluginRepositoryInterface $repository,
        private PluginSyncService $sync
    ) {}

    public function listPlugins(): array {

        $discovered = $this->discovery->discover();

        // Zorgt dat nieuwe plugins in DB komen
        $this->sync->sync($discovered);

        $installed = [];
        foreach ($this->repository->all() as $row) {
            $installed[$row['name']] = $row;
        }

        $result = [];

        foreach ($discovered as $plugin) {
            $db = $installed[$plugin->name] ?? null;

            $result[] = [
                'name'        => $plugin->name,
                'version'     => $plugin->version,
                'description' => $plugin->description,
                'installed'   => $db !== null,
                'enabled'     => $db ? (bool) $db['enabled'] : false,
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