<?php

namespace Keystone\Core\Dashboard;

use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Core\Plugin\PluginStatsService;
use Keystone\Core\Plugin\PluginUpdateService;
use Keystone\Core\Dashboard\DashboardActivityRepository;

final class DashboardService {
    
    public function __construct(
        private UserRepositoryInterface $users,
        private PluginStatsService $pluginStats,
        private PluginUpdateService $pluginUpdate,
        private DashboardActivityRepository $activityRepository,
    ) {}

    public function getStats(): DashboardStats
    {
        return new DashboardStats(
            users: $this->users->countAll(),
            plugins: $this->pluginStats->countInstalled(),
            pluginUpdates: $this->pluginUpdate->countUpdatesAvailable(),
        );
    }

    /**
     * @return DashboardActivity[]
     */
    public function getLatestActivity(int $limit = 5): array
    {
        return $this->activityRepository->latest($limit);
    }
}


?>