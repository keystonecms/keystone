<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Update;

final class ReleaseManifest {
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $releasedAt,
        public readonly string $minPhp,
        public readonly array $requiredFiles,
        public readonly array $requiredDirectories,
        public readonly array $sharedSymlinks
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            version: $data['version'],
            releasedAt: $data['released_at'],
            minPhp: $data['php']['min'],
            requiredFiles: $data['checks']['files'] ?? [],
            requiredDirectories: $data['checks']['directories'] ?? [],
            sharedSymlinks: $data['shared']['symlinks'] ?? []
        );
    }
}

?>