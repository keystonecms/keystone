<?php

namespace Keystone\Core\Plugin;

use Keystone\Core\Plugin\PluginSyncServiceInterface;
use Keystone\Core\Plugin\PluginRepositoryInterface;

final class PluginSyncService implements PluginSyncServiceInterface {
    
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


        $this->repository->install($plugin);
        
        // $this->repository->install(
        //     name: $plugin->name,
        //     version: $plugin->version,
        //     loadOrder: $plugin->loadOrder
        // );
    }
}

}


?>