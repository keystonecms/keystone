<?php

namespace Keystone\Core\Setup;

final class SetupConfig {

    public function __construct(
        public readonly string $envPath,
        public readonly string $lockFilePath,
        public readonly string $migrationPath
    ) {}

    public function isInstalled(): bool
    {
        return file_exists($this->lockFilePath);
    }

    public function hasEnv(): bool
    {
        return file_exists($this->envPath);
    }
}


?>