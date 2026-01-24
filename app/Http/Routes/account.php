<?php

use Keystone\Http\Controllers\Account\AccountController;
use Keystone\Http\Controllers\Account\AvatarController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Http\Controllers\Account\AccountSecurityController;

$app->group('/account', function ($group) {

$group->get('', [AccountController::class, 'index']);
$group->post('/password', [AccountController::class, 'changePassword']);
$group->post('/2fa/disable', [AccountController::class, 'disableTwoFactor']);
$group->post('/avatar', [AvatarController::class, 'upload'])->setName('account.avatar.upload');
$group->get('/security', [AccountSecurityController::class,'show'])->setName('account.security');
$group->post('/security', [AccountSecurityController::class,'update'])->setName('account.security.update');
})
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));


?>