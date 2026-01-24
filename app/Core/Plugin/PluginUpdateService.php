<?php

namespace Keystone\Core\Plugin;


final class PluginUpdateService {
    
    public function __construct() {}

    public function countUpdatesAvailable(): int
    {
     return 1;
    // return $this->updates->countAvailable();
    }
}

?>