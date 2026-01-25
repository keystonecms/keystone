<?php

namespace Keystone\Core\Plugin;

use Keystone\Core\Plugin\PluginRegistryInterface;
use Keystone\Core\Plugin\PluginUpdateService;

final class PluginStatsService {
    
    public function __construct(
        private PluginRegistryInterface $repository,
        private PluginUpdateService $updates
    ) {}

    public function countInstalled(): int
    {
        return $this->repository->count();
    }

    public function countUpdatesAvailable(): int
    {
     return $this->updates->countAvailable();
    }
}

?>