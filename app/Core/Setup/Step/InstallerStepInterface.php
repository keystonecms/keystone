<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;

interface InstallerStepInterface {
    public function run(InstallerState $state): void;
}


?>