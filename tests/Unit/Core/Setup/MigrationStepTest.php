<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Setup;

use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\MigrationStep;
use Keystone\Core\Setup\Database\MigrationRunnerInterface;
use Keystone\Tests\TestCase;

final class MigrationStepTest extends TestCase
{
public function test_migrations_run_on_empty_database(): void {
    $runner = $this->createMock(MigrationRunnerInterface::class);

    $runner
        ->expects($this->once())
        ->method('runFresh');

    $runner
        ->expects($this->never())
        ->method('runPending');

    $step = new MigrationStep($runner);

    $state = new InstallerState(
        dryRun: false,
        freshInstall: true
    );

    $step->run($state);

    $this->assertTrue(true);
    }
}

?>