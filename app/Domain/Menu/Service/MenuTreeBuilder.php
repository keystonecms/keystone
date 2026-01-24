<?php

declare(strict_types=1);

namespace Keystone\Domain\Menu\Service;

use Keystone\Domain\Menu\Entity\MenuItem;

final class MenuTreeBuilder
{
    /**
     * @param MenuItem[] $items
     * @return MenuItem[]
     */
    public function build(array $items, bool $onlyVisible = true): array
    {
        $indexed = [];

        // 1️⃣ Indexeer alle items (optioneel filter visible)
        foreach ($items as $item) {
            if ($onlyVisible && !$item->isVisible()) {
                continue;
            }

            // reset children (immutability)
            $indexed[$item->id()] = $item->withChildren([]);
        }

        $tree = [];

        // 2️⃣ Koppel children of markeer als root
        foreach ($indexed as $item) {
            $parentId = $item->parentId();

            if ($parentId !== null && isset($indexed[$parentId])) {
                $parent = $indexed[$parentId];

                $indexed[$parentId] = $parent->withChildren(
                    array_merge($parent->children(), [$item])
                );
            } else {
                // 👈 DIT was bij jou effectief afwezig
                $tree[] = $item;
            }
        }

        return $tree;
    }
}


?>