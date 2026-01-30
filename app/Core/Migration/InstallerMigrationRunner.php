<?php

namespace Keystone\Core\Migration;

use PDO;
use Keystone\Core\Setup\Database\MigrationRunnerInterface;

final class InstallerMigrationRunner implements MigrationRunnerInterface {
    
    public function __construct(
        private MigrationRunner $runner,
        private MigrationProvider $provider
    ) {}

    public function runWithPdo(PDO $pdo): void
    {
        $this->runner->run(
            $pdo,
            $this->provider->all()
        );
    }
}

?>


