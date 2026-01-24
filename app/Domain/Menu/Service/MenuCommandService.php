<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Service;

use Keystone\Domain\Menu\Repository\MenuWriteRepositoryInterface;

final class MenuCommandService {


    public function __construct(
        private MenuWriteRepositoryInterface $writeRepo
    ) {
    }

    public function createMenu(
        string $name,
        string $handle,
        ?string $description
    ): int {
        // business rule: handle is canonical
        $handle = strtolower(trim($handle));

        return $this->writeRepo->createMenu(
            $name,
            $handle,
            $description
        );
    }

public function addItemToMenu(
    int $menuId,
    ?int $parentId,
    string $label,
    string $linkType,
    string $linkTarget,
    ?string $cssClass,
    ?string $target
): void {
    $this->writeRepo->addMenuItem(
        $menuId,
        $parentId,
        $label,
        $linkType,
        $linkTarget,
        999,
        $cssClass,
        $target
    );
}


    public function updateMenuItem(
        int $id,
        string $label,
        bool $isVisible,
        ?string $cssClass,
        ?string $target
    ): void {
        $this->writeRepo->updateMenuItem(
            $id,
            $label,
            $isVisible,
            $cssClass,
            $target
        );
    }

    public function removeMenuItem(int $id): void
    {
        $this->writeRepo->deleteMenuItem($id);
    }
}


?>