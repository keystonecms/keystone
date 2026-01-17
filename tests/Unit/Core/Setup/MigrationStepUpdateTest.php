<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Setup;

use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\MigrationStep;
use Keystone\Core\Setup\Database\MigrationRunnerInterface;
use Keystone\Tests\TestCase;

final class MigrationStepUpdateTest extends TestCase
{
    public function test_pending_migrations_run_on_update(): void
    {
        $runner = $this->createMock(MigrationRunnerInterface::class);

        $runner->expects($this->once())
            ->method('runPending');

        $runner->expects($this->never())
            ->method('runFresh');

        $step = new MigrationStep($runner);

        $state = new InstallerState(
            dryRun: false,
            freshInstall: false
        );

        $step->run($state);

        $this->assertTrue(true);
    }
}

?>