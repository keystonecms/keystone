<?php

namespace Keystone\Core\Plugin;

final class ComposerRunner {

    public function require(string $package): void {
        $process = new Process([
            'composer',
            'require',
            $package,
            '--no-interaction',
            '--no-dev',
        ]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                $process->getErrorOutput()
            );
        }
    }
}
