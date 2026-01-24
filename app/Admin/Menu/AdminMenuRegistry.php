<?php
/**
 * plugin admin menu's in control panel
 */
namespace Keystone\Admin\Menu;

final class AdminMenuRegistry {

    private array $items = [];

    public function add(array $item): void {
        $this->items[] = $item;
    }

    public function all(?string $currentRoute = null): array
    {
        $items = $this->items;

        foreach ($items as &$item) {
            $item['active'] = $this->isActive($item, $currentRoute);
        }

        return $this->sort($items);
    }

    private function isActive(array &$item, ?string $currentRoute): bool
    {
        if (!$currentRoute) {
            return false;
        }

        // Direct match
        if (isset($item['route']) && $item['route'] === $currentRoute) {
            return true;
        }

        // Prefix match (admin.blog.*)
        if (isset($item['match']) && str_starts_with($currentRoute, $item['match'])) {
            return true;
        }

        // Children
        if (isset($item['children'])) {
            foreach ($item['children'] as &$child) {
                if ($this->isActive($child, $currentRoute)) {
                    $child['active'] = true;
                    return true;
                }
            }
        }

        return false;
    }

    private function sort(array $items): array
    {
        usort($items, fn ($a, $b) =>
            ($a['order'] ?? 100) <=> ($b['order'] ?? 100)
        );

        return $items;
    }
}

?>