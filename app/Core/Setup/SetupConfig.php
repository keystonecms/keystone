<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

final class SetupConfig
{
    public function __construct(
        public string $envPath,
        public string $lockFilePath,
        public string $migrationPath,
    ) {}
}


?>