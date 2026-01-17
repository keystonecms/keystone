<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

final class EnvironmentRequirements
{
    public function __construct(
        public readonly string $minPhpVersion = '8.2',
        public readonly array $requiredExtensions = [
            'pdo',
            'pdo_mysql',
            'json',
            'mbstring',
        ],
        public readonly array $writablePaths = [
            'storage',
            'cache',
        ],
    ) {}
}
