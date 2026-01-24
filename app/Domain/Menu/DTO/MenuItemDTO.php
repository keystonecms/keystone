<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\DTO;

final class MenuItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $menuId,
        public readonly ?int $parentId,
        public readonly string $label,
        public readonly string $linkType,
        public readonly string $linkTarget,
        public readonly int $sortOrder,
        public readonly bool $isVisible
    ) {
    }
}


?>