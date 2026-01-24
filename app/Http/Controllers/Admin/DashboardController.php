<?php

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Keystone\Core\Dashboard\DashboardService;
use Keystone\Core\Dashboard\DashboardWidgetService;

final class DashboardController {

public function __construct(
        private DashboardService $dashboard,
        private DashboardWidgetService $widgets,
        private Twig $view,
    ) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {

        return $this->view->render(
            $response,
            '@core/admin/dashboard/index.twig',
            [
                'stats'    => $this->dashboard->getStats(),
                'activity' => $this->dashboard->getLatestActivity(),
                'widgets' => $this->widgets->getWidgets(),
            ]
        );
    }
}


?>