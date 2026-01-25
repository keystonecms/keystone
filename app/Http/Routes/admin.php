<?php

use Keystone\Http\Controllers\Admin\PluginController;
use Keystone\Http\Controllers\Admin\PluginCatalogController;

use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Http\Controllers\Admin\MenuController;
use Keystone\Http\Controllers\Admin\DashboardController;
use Keystone\Http\Controllers\Admin\ErrorController;
use Keystone\Http\Middleware\RequirePolicy;
use Keystone\Http\Middleware\PolicyMiddleware;

$requirePolicy = $container->get(RequirePolicy::class);

$app->group('/admin', function ($group) use ($requirePolicy) {
 
    $group->get('/dashboard', [DashboardController::class, 'index'])->setName('admin.dashboard');

    $group->get('/menus', [MenuController::class, 'index']);
    $group->get('/menus/create', [MenuController::class, 'create']);
    $group->post('/menus', [MenuController::class, 'store']);

    $group->get('/menus/{id}', [MenuController::class, 'edit']);
    $group->post('/menus/{id}', [MenuController::class, 'update']);

    $group->post('/menus/{id}/items', [MenuController::class, 'storeItem']);
    $group->post('/menu-items/{id}/update', [MenuController::class, 'updateItem']);
    $group->post('/menu-items/{id}/delete', [MenuController::class, 'deleteItem']);

    $group->get('/system/errors', [ErrorController::class, 'index'])
        ->setName('system.errors.index')
        ->add($requirePolicy('system.errors.view'));

    $group->get('/system/errors/{id}', [ErrorController::class, 'show'])
        ->setName('system.errors.show')
        ->add($requirePolicy('system.errors.view'));

    $group->post('/system/errors/{id}/resolve', [ErrorController::class, 'resolve'])
        ->setName('system.errors.resolve')
        ->add($requirePolicy('system.errors.resolve'));
    })
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));

$app->group('/admin/plugins', function ($group) use ($requirePolicy) {

    $group->get('', PluginController::class . ':index');

    $group->get('/catalog', PluginCatalogController::class . ':index');

    $group->post('/{name}/install', PluginController::class . ':install');

    $group->post('/{name}/enable', PluginController::class . ':enable');
    $group->post('/{name}/disable', PluginController::class . ':disable');

})
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));



?>
