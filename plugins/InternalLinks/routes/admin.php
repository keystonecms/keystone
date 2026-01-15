<?php

use Keystone\Plugins\InternalLinks\Controller\Admin\InternalLinkController;

/** @var Slim\App $app */

$app->get(
    '/admin/internal-links/{type}/{id}',
    [InternalLinkController::class, 'index']
);

$app->post(
    '/admin/internal-links/{type}/{id}',
    [InternalLinkController::class, 'store']
);

$app->post(
    '/admin/internal-links/{type}/{id}/delete',
    [InternalLinkController::class, 'delete']
);

$app->get(
    '/admin/pages/{id}/links',
    [InternalLinkController::class, 'panel']
);

?>