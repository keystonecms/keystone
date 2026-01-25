<?php

namespace Keystone\Core\Plugin;

use Symfony\Component\Process\Process;
use RuntimeException;
use Keystone\Infrastructure\Paths;

final class ComposerRunner {

public function __construct(
     private Paths $paths
        ) {}



    public function assertAvailable(): void {
        $process = new Process(['composer', '--version']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'Composer is not available on this system.'
            );
        }
    }

    public function require(string $package): void {
        $this->run([
            'composer',
            'require',
            $package,
            '--no-interaction',
        ]);
    }

    public function update(string $package): void
    {
        $this->run([
            'composer',
            'update',
            $package,
            '--no-interaction',
        ]);
    }

    public function remove(string $package): void
    {
        $this->run([
            'composer',
            'remove',
            $package,
            '--no-interaction',
        ]);
    }

    private function run(array $command): void
    {
        $process = new Process($command, $this->paths->base());
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                $process->getErrorOutput() ?: $process->getOutput()
            );
        }
    }
}

?>
