<?php

namespace Keystone\Core\Setup;

use Keystone\Core\Setup\SetupConfig;

final class InstallerCommit {
    public function __construct(
        private SetupConfig $config
    ) {}

    public function handle(string $appUrl): void {

        if (!file_exists($this->config->envPath)) {
            throw new RuntimeException('.env file not found, cannot commit installation');
        }

        $env = file_get_contents($this->config->envPath);

        // idempotent: al geïnstalleerd → niks doen
        if (str_contains($env, 'APP_INSTALLED=true')) {
            return;
        }

        $env = rtrim($env) . PHP_EOL . 'APP_INSTALLED=true' . PHP_EOL;
        $env = rtrim($env) . PHP_EOL . 'APP_BASE_URL='. $appUrl . PHP_EOL;
        $env = rtrim($env) . PHP_EOL . 'APP_URL='. $appUrl . PHP_EOL;

        $written = file_put_contents($this->config->envPath,  $env, LOCK_EX);

        file_put_contents($this->config->lockFilePath, 'installed');

        if ($written === false) {
            throw new RuntimeException('Failed to write APP_INSTALLED flag');
        }
    }
}

?>