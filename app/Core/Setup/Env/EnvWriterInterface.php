<?php

declare(strict_types=1);

namespace Keystone\Core\Setup\Env;

interface EnvWriterInterface
{
    /**
     * @param array<string, string> $values
     */
    public function write(array $values): void;
}

?>