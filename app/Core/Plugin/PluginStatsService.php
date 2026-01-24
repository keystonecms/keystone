<?php

namespace Keystone\Core\Plugin;

use Keystone\Core\Plugin\PluginRepository;
use Keystone\Core\Plugin\PluginUpdateService;

final class PluginStatsService {
    
    public function __construct(
        private PluginRepository $repository,
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