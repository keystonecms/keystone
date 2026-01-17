<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Setup;

use Keystone\Core\Setup\UpdaterKernel;
use Keystone\Core\Setup\SetupKernel;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\InstallerStepInterface;
use Keystone\Tests\TestCase;

final class UpdaterKernelTest extends TestCase {
    public function test_updater_runs_all_steps(): void
    {
        $step1 = $this->createMock(InstallerStepInterface::class);
        $step2 = $this->createMock(InstallerStepInterface::class);

        $step1->expects($this->once())
            ->method('run')
            ->with($this->isInstanceOf(InstallerState::class));

        $step2->expects($this->once())
            ->method('run')
            ->with($this->isInstanceOf(InstallerState::class));

        $kernel = new SetupKernel([
            $step1,
            $step2,
        ]);

        $updater = new UpdaterKernel($kernel);

        $updater->run();

        $this->assertTrue(true);
    }
}
