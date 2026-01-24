<?php

namespace Keystone\Core\Dashboard;

final class DashboardWidgetService {

public function __construct(
        private DashboardWidgetRegistry $registry
    ) {}

    public function getWidgets(): array
    {
        $widgets = [];

        foreach ($this->registry->all() as $widget) {
            $widgets[] = [
                'id'    => $widget->getId(),
                'title' => $widget->getTitle(),
                'html'  => $widget->render(),
            ];
        }

        return $widgets;
    }
}

?>
