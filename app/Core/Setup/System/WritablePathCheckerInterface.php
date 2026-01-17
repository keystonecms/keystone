<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\System;

interface WritablePathCheckerInterface
{
    public function isWritable(string $path): bool;
}


?>