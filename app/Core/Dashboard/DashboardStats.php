<?php

namespace Keystone\Core\Dashboard;

final class DashboardStats
{
    public function __construct(
        public readonly int $users,
        public readonly int $plugins,
        public readonly int $pluginUpdates,
    ) {}
}


?>