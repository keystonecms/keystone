<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Database;

interface MigrationRunnerInterface
{
    public function runFresh(): void;
    public function runPending(): void;
}

?>