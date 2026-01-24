<?php

declare(strict_types=1);

namespace Keystone\Core\Theme;

final class Theme
{
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly ThemeManifest $manifest
    ) {}
}

?>