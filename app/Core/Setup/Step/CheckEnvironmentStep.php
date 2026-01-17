<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\EnvironmentRequirements;
use Keystone\Core\Setup\Exception\EnvironmentCheckFailed;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\System\PhpVersionCheckerInterface;
use Keystone\Core\Setup\System\PhpExtensionCheckerInterface;
use Keystone\Core\Setup\System\WritablePathCheckerInterface;

final class CheckEnvironmentStep implements InstallerStepInterface
{
    public function __construct(
        private PhpVersionCheckerInterface $phpVersionChecker,
        private PhpExtensionCheckerInterface $extensionChecker,
        private WritablePathCheckerInterface $pathChecker,
        private EnvironmentRequirements $requirements,
    ) {}

    public function run(InstallerState $state): void
    {
        $errors = [];

        if (!$this->phpVersionChecker->isSatisfied($this->requirements->minPhpVersion)) {
            $errors[] = 'PHP version too low';
        }

        foreach ($this->requirements->requiredExtensions as $extension) {
            if (!$this->extensionChecker->isLoaded($extension)) {
                $errors[] = sprintf('Missing extension: %s', $extension);
            }
        }

        foreach ($this->requirements->writablePaths as $path) {
            if (!$this->pathChecker->isWritable($path)) {
                $errors[] = sprintf('Path not writable: %s', $path);
            }
        }

        if ($errors !== []) {
            throw new EnvironmentCheckFailed(implode("\n", $errors));
        }
    }
}


?>