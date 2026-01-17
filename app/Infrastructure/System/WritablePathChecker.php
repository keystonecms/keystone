<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\System;

use Keystone\Core\Setup\System\WritablePathCheckerInterface;

final class WritablePathChecker implements WritablePathCheckerInterface
{
    public function isWritable(string $path): bool
    {
        return is_writable(BASE_PATH . '/' . $path);
    }
}
