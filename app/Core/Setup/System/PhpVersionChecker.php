<?php

namespace Keystone\Core\Setup\System;

use Keystone\Core\Setup\System\PhpVersionCheckerInterface;

final class PhpVersionChecker implements PhpVersionCheckerInterface {
    public function isSatisfied(string $minVersion): bool {

        return version_compare(PHP_VERSION, $minVersion, '>=');
    }
}


?>

