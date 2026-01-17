<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\Database\MigrationRunnerInterface;
use Keystone\Core\Setup\InstallerState;

final class MigrationStep implements InstallerStepInterface
{
    public function __construct(
        private MigrationRunnerInterface $runner
    ) {}

    public function run(InstallerState $state): void
    {
        if ($state->dryRun) {
            return;
        }

        if ($state->isFreshInstall()) {
            $this->runner->runFresh();
        } else {
            $this->runner->runPending();
        }
    }
}
