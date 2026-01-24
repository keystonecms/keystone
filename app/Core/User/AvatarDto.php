<?php

namespace Keystone\Core\User;

final class AvatarDto {
    
    public function __construct(
        private string $path,
    ) {}

    public function path(): string
    {
        return $this->path;
    }
}

?>