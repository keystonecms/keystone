<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\System;

interface PhpExtensionCheckerInterface
{
    public function isLoaded(string $extension): bool;
}



?>