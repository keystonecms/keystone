<?php

use Keystone\Plugins\Auth\Controller\Public\RegisterController;
use Keystone\Plugins\Auth\Controller\Public\ForgotPasswordController;
use Keystone\Plugins\Auth\Controller\Public\ResetPasswordController;
use Keystone\Plugins\Auth\Controller\Public\ActivateController;
use Keystone\Plugins\Auth\Controller\Public\TwoFactorController;

$app->get('/register', [RegisterController::class, 'show']);
$app->post('/register', [RegisterController::class, 'submit']);
$app->get('/forgot-password', [ForgotPasswordController::class, 'show']);
$app->post('/forgot-password', [ForgotPasswordController::class, 'submit']);
$app->get('/reset-password', [ResetPasswordController::class, 'show']);
$app->post('/reset-password', [ResetPasswordController::class, 'submit']);
$app->get('/activate', ActivateController::class);
$app->get('/2fa', [TwoFactorController::class, 'show']);
$app->post('/2fa', [TwoFactorController::class, 'verify']);

?>
