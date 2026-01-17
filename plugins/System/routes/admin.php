<?php

use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Plugins\System\Controller\Admin\UpdateController;

$app->get('/admin/system/update', [UpdateController::class, 'index']);
$app->post('/admin/system/update/dry-run', [UpdateController::class, 'dryRun']);
$app->post('/admin/system/update/activate', [UpdateController::class, 'activate'])
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));
?>