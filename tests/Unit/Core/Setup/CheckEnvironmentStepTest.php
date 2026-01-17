<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Core\Setup;

use Keystone\Core\Setup\EnvironmentRequirements;
use Keystone\Core\Setup\Exception\EnvironmentCheckFailed;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Step\CheckEnvironmentStep;
use Keystone\Core\Setup\System\PhpExtensionCheckerInterface;
use Keystone\Core\Setup\System\PhpVersionCheckerInterface;
use Keystone\Core\Setup\System\WritablePathCheckerInterface;
use Keystone\Tests\TestCase;

final class CheckEnvironmentStepTest extends TestCase
{
    public function test_it_fails_when_extension_is_missing(): void
    {
        $php = $this->createMock(PhpVersionCheckerInterface::class);
        $ext = $this->createMock(PhpExtensionCheckerInterface::class);
        $path = $this->createMock(WritablePathCheckerInterface::class);

        $php->method('isSatisfied')->willReturn(true);
        $ext->method('isLoaded')->willReturn(false);
        $path->method('isWritable')->willReturn(true);

        $step = new CheckEnvironmentStep(
            $php,
            $ext,
            $path,
            new EnvironmentRequirements()
        );

        $this->expectException(EnvironmentCheckFailed::class);

        $step->run(new InstallerState());
    }
}
