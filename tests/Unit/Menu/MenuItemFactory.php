<?php

namespace Keystone\Tests\Unit\Menu;

use Keystone\Domain\Menu\Entity\MenuItem;

final class MenuItemFactory
{
    public static function make(
        int $id,
        ?int $parentId = null,
        bool $visible = true
    ): MenuItem {
        return new MenuItem(
            id: $id,
            menuId: 1,
            parentId: $parentId,
            label: "Item {$id}",
            linkType: 'url',
            linkTarget: 'https://example.com',
            sortOrder: $id,
            isVisible: $visible,
            cssClass: null,
            target: null,
            children: []
        );
    }
}


?>