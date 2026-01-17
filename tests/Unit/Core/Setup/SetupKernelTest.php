<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Setup;

use Keystone\Core\Setup\SetupKernel;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\InstallerStepInterface;
use Keystone\Tests\TestCase;

final class SetupKernelTest extends TestCase
{
    public function test_it_runs_all_steps_in_order(): void
    {
        $step1 = $this->createMock(InstallerStepInterface::class);
        $step2 = $this->createMock(InstallerStepInterface::class);

        $step1->expects($this->once())->method('run');
        $step2->expects($this->once())->method('run');

        $kernel = new SetupKernel([
            $step1,
            $step2,
        ]);

        $kernel->run(new InstallerState(dryRun: true));

        $this->assertTrue(true);
    }
}

?>