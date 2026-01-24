<?php

declare(strict_types=1);

namespace Keystone\Tests\Unit\Menu;

use Keystone\Domain\Menu\Service\MenuTreeBuilder;
use PHPUnit\Framework\TestCase;

final class MenuTreeBuilderTest extends TestCase {
    public function test_it_returns_empty_array_for_empty_input(): void
    {
        $builder = new MenuTreeBuilder();

        $tree = $builder->build([], onlyVisible: false);

        self::assertSame([], $tree);
    }

    public function test_it_builds_flat_menu_when_no_parents(): void
    {
        $builder = new MenuTreeBuilder();

        $items = [
            MenuItemFactory::make(1),
            MenuItemFactory::make(2),
            MenuItemFactory::make(3),
        ];

        $tree = $builder->build($items, onlyVisible: false);

        self::assertCount(3, $tree);
        self::assertSame(1, $tree[0]->id());
        self::assertSame(2, $tree[1]->id());
        self::assertSame(3, $tree[2]->id());
    }

public function test_it_builds_nested_tree(): void
{
    $builder = new MenuTreeBuilder();

    $items = [
        MenuItemFactory::make(1),
        MenuItemFactory::make(2, parentId: 1),
        MenuItemFactory::make(3, parentId: 1),
        MenuItemFactory::make(4),
    ];

    self::assertCount(4, $items);

    $tree = $builder->build($items, onlyVisible: false);

    self::assertCount(2, $tree);
}


    public function test_it_filters_invisible_items_when_only_visible_is_true(): void
    {
        $builder = new MenuTreeBuilder();

        $items = [
            MenuItemFactory::make(1),
            MenuItemFactory::make(2, parentId: 1, visible: false),
            MenuItemFactory::make(3, parentId: 1),
        ];

        $tree = $builder->build($items, onlyVisible: true);

        self::assertCount(1, $tree);
        self::assertCount(1, $tree[0]->children());
        self::assertSame(3, $tree[0]->children()[0]->id());
    }
}


?>