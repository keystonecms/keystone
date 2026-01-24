<?php

namespace Keystone\Core\User;

final class StoredAvatar {
    public function __construct(
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $size,
    ) {}
}

?>