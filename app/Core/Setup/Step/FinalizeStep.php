<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Step;

use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\SetupConfig;
use RuntimeException;

final class FinalizeStep extends AbstractInstallerStep {


    public function __construct(
        private EnvWriterInterface $envWriter,
        private SetupConfig $config
    ) {}

    public function getName(): string {
        return 'finalize';
    }

    public function getTitle(): string {
    return 'Finalize the Installation';
    }

    public function getDescription(): string {
        return 'Finalize installation description.';
        }


    public function shouldRun(InstallerState $state): bool {
        return true;
    }

    public function run(InstallerState $state): void {

    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

    $this->envWriter->write([
            'DB_DSN'  => 'mysql:host='.$state->dbHost.';dbname='.$state->dbName,
            'DB_HOST' => $state->dbHost,
            'DB_NAME' => $state->dbName,
            'DB_USER' => $state->dbUser,
            'DB_PASS' => $state->dbPass,
            'APP_DEBUG' => '0',
            'APP_ENV' => 'prod',
            'SITENAME' => 'Keystone CSM 4U',
        ]);


    }
}

?>
