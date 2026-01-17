<?php

declare(strict_types=1);

use Keystone\Http\Controller\InstallController;
use Slim\Routing\RouteCollectorProxy;

return static function (\Slim\App $app): void {

    $app->group('/install', function (RouteCollectorProxy $group): void {

        // UI shell
        $group->get('', InstallController::class . ':index');

        // AJAX / JSON
        $group->post('/check', InstallController::class . ':check');
        $group->post('/database', InstallController::class . ':database');
        $group->post('/admin', InstallController::class . ':admin');

    });
};


?>