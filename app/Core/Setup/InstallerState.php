<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

final class InstallerState
{
    public function __construct(
        public bool $dryRun = false,
        public bool $freshInstall = false,

        public ?string $dbHost = null,
        public ?string $dbName = null,
        public ?string $dbUser = null,
        public ?string $dbPass = null,
        public int $dbPort = 3306,

        public ?string $adminEmail = null,
        public ?string $adminPassword = null,
    ) {}

    public function isFreshInstall(): bool
    {
        return $this->freshInstall;
    }
}
?>