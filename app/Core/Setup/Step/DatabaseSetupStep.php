<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\Database\DatabaseCreatorInterface;
use Keystone\Core\Setup\InstallerState;

final class DatabaseSetupStep implements InstallerStepInterface
{
    public function __construct(
        private DatabaseCreatorInterface $databaseCreator
    ) {}

    public function run(InstallerState $state): void
    {
        if ($state->dryRun || !$state->dbHost || !$state->dbName) {
            return;
        }

        $this->databaseCreator->createIfNotExists(
            $state->dbHost,
            $state->dbName,
            $state->dbUser ?? '',
            $state->dbPass ?? ''
        );
    }
}

?>
