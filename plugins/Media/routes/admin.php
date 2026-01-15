<?php

use Keystone\Plugins\Media\Controller\Admin\MediaController;

$app->get(
    '/admin/media',
    [MediaController::class, 'index']
)->setName('media.index');

$app->post(
    '/admin/media/upload',
    [MediaController::class, 'upload']
)->setName('media.upload');

$app->post(
    '/admin/media/{id}/delete',
    [MediaController::class, 'delete']
)->setName('media.delete');

?>