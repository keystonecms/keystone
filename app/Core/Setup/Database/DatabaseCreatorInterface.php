<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Database;

interface DatabaseCreatorInterface {
    public function createIfNotExists(
        string $host,
        string $database,
        string $user,
        string $password,
        int $port = 3306
    ): void;
}

?>