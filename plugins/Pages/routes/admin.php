<?php

use Keystone\Plugins\Pages\Controller\Admin\PageController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;

/** @var Slim\App $app */
/** @var Psr\Container\ContainerInterface $container */

$app->group('/admin/pages', function ($group) {
$group->get('', PageController::class . ':index');
$group->get('/create', PageController::class . ':form');
$group->get('/{id:\d+}/edit', PageController::class . ':form');
$group->post('/save', PageController::class . ':save');
$group->post('/{id:\d+}/delete', PageController::class . ':delete');
})
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));


?>