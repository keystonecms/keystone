<?php

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;

interface InstallerStepInterface {

    public function getName(): string;

    public function shouldRun(InstallerState $state): bool;

    public function run(InstallerState $state): void;
}

?>
