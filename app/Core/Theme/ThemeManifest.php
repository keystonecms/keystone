<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

final class ThemeManifest
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly ?string $extends = null
    ) {}
}


?>