<?php

namespace Keystone\Core\Plugin;

final class PluginSyncService {
    
    public function __construct(
        private PluginRepositoryInterface $repository
    ) {}

    /**
     * @param PluginDescriptor[] $discovered
     */
public function sync(array $discovered): void {
    foreach ($discovered as $plugin) {

        if ($this->repository->find($plugin->name)) {
            continue;
        }

        $this->repository->install(
            name: $plugin->name,
            version: $plugin->version,
            loadOrder: $plugin->loadOrder
        );
    }
}

}


?>