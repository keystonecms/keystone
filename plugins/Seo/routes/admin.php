<?php

use Keystone\Plugins\Seo\Controller\Admin\SeoController;

$app->get(
    '/admin/seo/{type}/{id}',
    [SeoController::class, 'edit']
)->setName('admin.seo.edit');

$app->post(
    '/admin/seo/{type}/{id}',
    [SeoController::class, 'update']
)->setName('admin.seo.update');



?>