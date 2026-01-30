<?php

declare(strict_types=1);

namespace Keystone\Core\Setup;

final class EnvironmentRequirements
{
    public function __construct(
        public readonly string $minPhpVersion = '8.3',

        /** @var non-empty-string[] */
        public readonly array $requiredExtensions = [
            'pdo',
            'pdo_mysql',
            'json',
            'mbstring',
            'zip',
        ],

        /** @var non-empty-string[] */
        public readonly array $writablePaths = [
            'storage',
            'cache',
        ],
    ) {}
}

?>
