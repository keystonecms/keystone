<?php

use Keystone\Http\Controllers\Admin\PluginController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;

$app->get('/admin/plugins', [PluginController::class, 'index']
)
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));


?>
