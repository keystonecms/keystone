<?php

namespace Keystone\Core\Dashboard;

interface DashboardWidgetInterface {
    
    public function getId(): string;

    public function getTitle(): string;

    public function render(): string;
}


?>