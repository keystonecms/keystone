<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Database;

use PDO;

interface MigrationRunnerInterface {

    public function runWithPdo(PDO $pdo): void;
}


?>

