<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use App\Core\Setup\InstallerState;

interface InstallerStep {
    public function run(InstallerState $state): void;
}

?>