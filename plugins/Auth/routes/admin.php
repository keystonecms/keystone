<?php

use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Plugins\Auth\Controller\Admin\TwoFactorEnrollmentController;
use Keystone\Plugins\Auth\Controller\Admin\AdminUsersController;


$app->get('/admin/2fa', [TwoFactorEnrollmentController::class, 'show']);
$app->post('/admin/2fa', [TwoFactorEnrollmentController::class, 'confirm']);
$app->get('/admin/users', [AdminUsersController::class, 'index']);
$app->post('/admin/users/status', [AdminUsersController::class, 'updateStatus'])
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));

?>