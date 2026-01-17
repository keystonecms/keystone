<?php

declare(strict_types=1);

namespace App\Core\Setup\Step;

use App\Core\Setup\InstallerState;

interface InstallerStep {
    public function run(InstallerState $state): void;
}

?>