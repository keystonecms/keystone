<?php

namespace Keystone\Core\Plugin\Update;

final class PluginUpdateResolver {
    
    public function hasUpdate(
        string $installedVersion,
        string $remoteVersion
    ): bool {
        return version_compare(
            $remoteVersion,
            $installedVersion,
            '>'
        );
    }
}


?>