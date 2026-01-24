<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\DTO;

final class MenuDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $handle,
        public readonly ?string $description
    ) {
  }
}

?>