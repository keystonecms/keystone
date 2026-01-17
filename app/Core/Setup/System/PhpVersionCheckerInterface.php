<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\System;

interface PhpVersionCheckerInterface
{
    public function isSatisfied(string $minimum): bool;
}


?>