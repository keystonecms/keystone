<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\System;

use Keystone\Core\Setup\System\PhpVersionCheckerInterface;

final class PhpVersionChecker implements PhpVersionCheckerInterface
{
    public function isSatisfied(string $minimum): bool
    {
        return version_compare(PHP_VERSION, $minimum, '>=');
    }
}


?>