<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Repository;

interface MenuWriteRepositoryInterface {
    
    public function createMenu(
        string $name,
        string $handle,
        ?string $description
    ): int;

    public function addMenuItem(
        int $menuId,
        ?int $parentId,
        string $label,
        string $linkType,
        string $linkTarget,
        int $sortOrder
    ): void;

    public function updateMenuItem(
        int $id,
        string $label,
        bool $isVisible,
        ?string $cssClass,
        ?string $target
    ): void;

    public function deleteMenuItem(int $id): void;
}


?>