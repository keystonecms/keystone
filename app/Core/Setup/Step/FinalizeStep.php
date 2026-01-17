<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\SetupConfig;
use RuntimeException;

final class FinalizeStep implements InstallerStepInterface
{
    public function __construct(
        private EnvWriterInterface $envWriter,
        private SetupConfig $config
    ) {}

    public function run(InstallerState $state): void
    {
        if ($state->dryRun) {
            return;
        }

        $this->envWriter->write([
            'APP_ENV'    => 'production',
            'APP_DEBUG' => '0',
            'DB_HOST'   => $state->dbHost,
            'DB_PORT'   => (string) $state->dbPort,
            'DB_NAME'   => $state->dbName,
            'DB_USER'   => $state->dbUser ?? '',
            'DB_PASS'   => $state->dbPass ?? '',
        ]);

        if (!file_exists($this->config->lockFilePath)) {
            file_put_contents($this->config->lockFilePath, 'installed');
        }
    }
}
