<?php

namespace Keystone\Core\Setup\System;

use Keystone\Core\Setup\System\PhpExtensionCheckerInterface;

final class PhpExtensionChecker implements PhpExtensionCheckerInterface {
    
    public function isLoaded(string $extension): bool {
        return extension_loaded($extension);
    }
}


?>