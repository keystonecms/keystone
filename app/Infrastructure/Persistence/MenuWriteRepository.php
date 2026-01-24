<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Persistence;

use PDO;
use Keystone\Domain\Menu\Repository\MenuWriteRepositoryInterface;

final class MenuWriteRepository implements MenuWriteRepositoryInterface {
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function createMenu(
        string $name,
        string $handle,
        ?string $description
    ): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO menus (name, handle, description, created_at, updated_at)
             VALUES (:name, :handle, :description, NOW(), NOW())'
        );

        $stmt->execute([
            'name' => $name,
            'handle' => $handle,
            'description' => $description,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function addMenuItem(
        int $menuId,
        ?int $parentId,
        string $label,
        string $linkType,
        string $linkTarget,
        int $sortOrder
    ): void {
        $stmt = $this->pdo->prepare('
                INSERT INTO menu_items
                (menu_id, parent_id, label, link_type, link_target, sort_order, is_visible, css_class, link_target_attr)
                VALUES
                (:menu_id, :parent_id, :label, :link_type, :link_target, :sort_order, 1, :css_class, :target)
       ');

        $stmt->execute([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'label' => $label,
            'link_type' => $linkType,
            'link_target' => $linkTarget,
            'sort_order' => $sortOrder,
            'css_class' => $cssClass,
            'target'    => $target,
        ]);
    }

    public function updateMenuItem(
        int $id,
        string $label,
        bool $isVisible,
        ?string $cssClass,
        ?string $target
   
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE menu_items
             SET label = :label,
                 is_visible = :is_visible,
                 css_class = :css_class,
                 link_target_attr = :target,
                 updated_at = NOW()
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'label' => $label,
            'is_visible' => $isVisible ? 1 : 0,
            'css_class' => $cssClass,
            'target' => $target,

        ]);
    }

    public function deleteMenuItem(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM menu_items WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
    }
}

?>