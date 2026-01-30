<?php

namespace Keystone\Core\Setup\System;

use Keystone\Core\Setup\System\WritablePathCheckerInterface;

final class WritablePathChecker implements WritablePathCheckerInterface {
    

    public function isWritable(string $path): bool {
        return is_writable($path);
    }
}


?>