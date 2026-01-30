<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Env;

use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\SetupConfig;

final class EnvFileWriter implements EnvWriterInterface {

    public function __construct(
        private SetupConfig $config
    ) {}

    public function write(array $values): void {
        $lines = [];

        foreach ($values as $key => $value) {
            $escaped = str_replace('"', '\"', (string) $value);
            $lines[] = sprintf('%s="%s"', $key, $escaped);
        }

        file_put_contents(
            $this->config->envPath,
            implode(PHP_EOL, $lines) . PHP_EOL,
            LOCK_EX
        );
    }
}

?>