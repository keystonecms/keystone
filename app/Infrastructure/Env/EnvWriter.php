<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Env;

use Keystone\Core\Setup\Env\EnvWriterInterface;
use Keystone\Core\Setup\SetupConfig;
use RuntimeException;

final class EnvWriter implements EnvWriterInterface
{
    public function __construct(
        private SetupConfig $config
    ) {}

    public function write(array $values): void
    {
        $envPath = $this->config->envPath;

        $content = '';
        foreach ($values as $key => $value) {
            $content .= sprintf("%s=%s\n", $key, $this->escape($value));
        }

        $tmp = $envPath . '.tmp';

        if (file_put_contents($tmp, $content, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write .env temp file');
        }

        rename($tmp, $envPath);
    }

    private function escape(string $value): string
    {
        return preg_match('/\s/', $value)
            ? '"' . addslashes($value) . '"'
            : $value;
    }
}

?>