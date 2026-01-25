<?php

namespace Keystone\Core\Plugin;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Keystone\Infrastructure\Paths;

final class PluginInstallerService {

    public function __construct(
        private PluginRegistry $registry,
        private LoggerInterface $logger,
        private Paths $paths
    ) {}

public function install(string $package): void {

    $this->assertComposerAvailable();

    if ($this->registry->existsByPackage($package)) {
        throw new RuntimeException(
            "Plugin [$package] is already installed."
        );
    }

    $this->logger->info('Installing plugin via Composer', [
        'package' => $package,
    ]);

    $this->runComposer([
        'require',
        $package,
    ]);
}

public function update(string $package): void {

        if (!$this->registry->existsByPackage($package)) {
            throw new RuntimeException(
                "Plugin [$package] is not installed."
            );
        }

        $this->logger->info('Updating plugin via Composer', [
            'package' => $package,
        ]);

        $this->runComposer([
            'update',
            $package,
        ]);
    }

public function remove(string $package): void {

        if (!$this->registry->existsByPackage($package)) {
            throw new RuntimeException(
                "Plugin [$package] is not installed."
            );
        }

        $this->logger->info('Removing plugin via Composer', [
            'package' => $package,
        ]);

        $this->runComposer([
            'remove',
            $package,
        ]);

        $this->registry->removeByPackage($package);
    }

private function runComposer(array $args): void {


        if (!file_exists($this->paths->base() . '/composer.json')) {
            throw new RuntimeException('composer.json not found at '. $this->paths->base());
        }


        $cmd = array_merge(
            ['composer'],
            $args,
            ['--no-interaction']
        );

        $command = implode(' ', array_map('escapeshellarg', $cmd));

        $this->logger->debug('Running composer command', [
            'command' => $command,
        ]);

        $process = proc_open(
            $command,
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $this->paths->base()
        );

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start Composer process.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            $this->logger->error('Composer failed', [
                'stdout' => $stdout,
                'stderr' => $stderr,
            ]);

            throw new RuntimeException(
                "Composer failed:\n" . trim($stderr)
            );
        }

        $this->logger->info('Composer finished successfully');
    }

/**
 * Check of composer is installed and executable  
 */    
private function assertComposerAvailable(): void {
    $process = proc_open(
        'composer --version',
        [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        $this->projectRoot
    );

    if (!is_resource($process)) {
        throw new RuntimeException('Unable to execute composer.');
    }

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new RuntimeException(
            'Composer is not available. Please install Composer to manage plugins.'
        );
    }
}


}


?>