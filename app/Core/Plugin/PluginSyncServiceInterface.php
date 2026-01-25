<?php

namespace Keystone\Core\Plugin;

interface PluginSyncServiceInterface {
    public function sync(array $discovered): void;
}

?>