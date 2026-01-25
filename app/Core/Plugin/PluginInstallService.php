<?php

namespace Keystone\Core\Plugin;

final class PluginInstallService {


    public function __construct(
        private ComposerRunner $composer,
        private PluginDiscovery $discovery,
        private PluginSyncService $sync
    ) {}

    public function install(string $package): void
    {
        $this->composer->require($package);

        $plugins = $this->discovery->discover();
        $this->sync->sync($plugins);
    }
}


?>