<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

final class UpdaterKernel {
    
    public function __construct(
        private SetupKernel $kernel
    ) {}

    public function run(): void
    {
        $state = new InstallerState(
            dryRun: false
        );

        $this->kernel->run($state);
    }
}

?>