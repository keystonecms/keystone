<?php

namespace Keystone\Core\Plugin;

interface PluginDiscoveryInterface {
    /** @return object[] */
    public function discover(): array;
}


?>