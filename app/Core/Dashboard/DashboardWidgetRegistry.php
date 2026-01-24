<?php

namespace Keystone\Core\Dashboard;

final class DashboardWidgetRegistry {

/** @var DashboardWidgetInterface[] */
    private array $widgets = [];

    public function add(DashboardWidgetInterface $widget): void
    {
        $this->widgets[$widget->getId()] = $widget;
    }

    /**
     * @return DashboardWidgetInterface[]
     */
    public function all(): array
    {
        return array_values($this->widgets);
    }
}

?>