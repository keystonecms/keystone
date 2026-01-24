<?php

declare(strict_types=1);

use Keystone\Http\Controllers\Admin\ThemeController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Core\Http\Middleware\PolicyMiddleware;

$app->group('/admin/themes', function ($group) {
$group->get('', [ThemeController::class, 'index'])->setName('admin.themes.index');
$group->post('/activate', [ThemeController::class, 'activate'])->setName('admin.themes.activate');
$group->post('/upload', [ThemeController::class, 'upload']);
$group->post('/uninstall', [ThemeController::class, 'uninstall']);
})
->add(AuthMiddleware::class)
// ->add(new PolicyMiddleware('manage_themes'))
->add(CsrfMiddleware::class);

