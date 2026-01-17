<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

use Keystone\Core\Setup\Step\InstallerStepInterface;

final class SetupKernel
{
    /**
     * @param iterable<InstallerStepInterface> $steps
     */
    public function __construct(
        private iterable $steps
    ) {}

    public function run(InstallerState $state): void
    {
        foreach ($this->steps as $step) {
            $step->run($state);
        }
    }
}

?>