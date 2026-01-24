<?php

declare(strict_types=1);

namespace Keystone\Infrastructure\Persistence;

use PDO;
use Keystone\Domain\Menu\Entity\Menu;
use Keystone\Domain\Menu\Entity\MenuItem;
use Keystone\Domain\Menu\Repository\MenuRepositoryInterface;
use Keystone\Domain\Menu\Service\MenuTreeBuilder;

final class MenuRepository implements MenuRepositoryInterface {


    public function __construct(
        private PDO $pdo,
        private MenuTreeBuilder $treeBuilder
    ) {
    }

public function getAll(): array
{
    $stmt = $this->pdo->query(
        'SELECT * FROM menus ORDER BY name'
    );

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(
        fn (array $row) => new Menu(
            id: (int) $row['id'],
            name: $row['name'],
            handle: $row['handle'],
            description: $row['description'],
            items: [] // 
        ),
        $rows
    );
}


    public function getByHandle(string $handle): ?Menu
    {
        $menuRow = $this->fetchMenu($handle);

        if ($menuRow === null) {
            return null;
        }

        $itemRows = $this->fetchMenuItems((int) $menuRow['id']);

        $items = array_map(
            fn (array $row) => $this->mapMenuItem($row),
            $itemRows
        );


        $tree = $this->treeBuilder->build($items);


         return new Menu(
            id: (int) $menuRow['id'],
            name: $menuRow['name'],
            handle: $menuRow['handle'],
            description: $menuRow['description'],
            items: $tree
        );
    }

    public function getById(int $id): ?Menu {
    $stmt = $this->pdo->prepare(
        'SELECT * FROM menus WHERE id = :id LIMIT 1'
    );


    $stmt->execute(['id' => $id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        return null;
    }

    // items ophalen + tree bouwen (zoals getByHandle)
    $itemRows = $this->fetchMenuItems((int) $row['id']);

    $items = array_map(
        fn (array $item) => $this->mapMenuItem($item),
        $itemRows
    );
    $tree = $this->treeBuilder->build($items);

    return new Menu(
        id: (int) $row['id'],
        name: $row['name'],
        handle: $row['handle'],
        description: $row['description'],
        items: $tree
    );
}

    private function fetchMenu(string $handle): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM menus WHERE handle = :handle LIMIT 1'
        );

        $stmt->execute(['handle' => $handle]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    private function fetchMenuItems(int $menuId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM menu_items WHERE menu_id = :menu_id'
        );

        $stmt->execute(['menu_id' => $menuId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function mapMenuItem(array $row): MenuItem
    {

    return new MenuItem(
        id: (int) $row['id'],
        menuId: (int) $row['menu_id'],
        parentId: $row['parent_id'] ? (int) $row['parent_id'] : null,
        label: $row['label'],
        linkType: $row['link_type'],
        linkTarget: $row['link_target'],
        sortOrder: (int) $row['sort_order'],
        isVisible: (bool) $row['is_visible'],
        cssClass: $row['css_class'] ?? null,
        target: $row['link_target_attr'] ?? null
            );
    }
}


?>