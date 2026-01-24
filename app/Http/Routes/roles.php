<?php

use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\RequirePolicy;
use Keystone\Http\Middleware\PolicyMiddleware;
use Keystone\Http\Controllers\Admin\RoleController;
use Keystone\Http\Controllers\Admin\PolicyController;

$requirePolicy = $container->get(RequirePolicy::class);

$app->group('/admin', function ($group) use ($requirePolicy) {

    $group->get('/roles', [RoleController::class, 'index'])
        ->setName('roles.index')
        ->add($requirePolicy('roles.index'));

    $group->get('/roles/create', [RoleController::class, 'create'])
        ->setName('roles.create')
        ->add($requirePolicy('roles.create'));

        $group->get('/roles/{id}', [RoleController::class, 'edit'])
        ->setName('admin.roles.edit');

    $group->post('/roles/{id}', [RoleController::class, 'update'])
        ->setName('admin.roles.update');


    $group->post('/roles', [RoleController::class, 'store'])
        ->setName('roles.store')
        ->add($requirePolicy('roles.manage'));

    $group->get('/policies', [PolicyController::class, 'index'])
        ->setName('admin.policies');

})
->add(AuthMiddleware::class);



?>