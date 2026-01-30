<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\InstallerState;
use PDO;

abstract class AbstractInstallerStep implements InstallerStepInterface {

    protected function createPdoFromState(InstallerState $state): PDO {
        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $state->dbHost === 'localhost' ? '127.0.0.1' : $state->dbHost,
                $state->dbPort ?? 3306,
                $state->dbName
            ),
            $state->dbUser,
            $state->dbPass ?? '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function shouldRun(InstallerState $state): bool {
        return true;
        
    }
}


?>