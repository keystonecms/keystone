<?php

use Keystone\Http\Controllers\Account\AccountController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;

$app->get('/account', [AccountController::class, 'index']);
$app->post('/account/password', [AccountController::class, 'changePassword']);
$app->post('/account/2fa/disable', [AccountController::class, 'disableTwoFactor'])
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));


?>