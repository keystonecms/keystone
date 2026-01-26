<?php

/*
 * Keystone CMS
 *
 * @author Constan van Suchtelen van de Haere <constan.vansuchtelenvandehaere@hostingbe.com>
 * @copyright 2026 HostingBE
 * @package   Keystone CMS
 * @author    HostingBE
 * @license   MIT
 * @link      https://keystone-cms.com
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF
 * OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

use Keystone\Http\Controllers\Admin\UpdateController;
use Keystone\Http\Controllers\Admin\ErrorController;
use Keystone\Http\Middleware\AuthMiddleware;
use Keystone\Http\Middleware\CsrfMiddleware;
use Keystone\Http\Middleware\RequirePolicy;
use Keystone\Http\Middleware\PolicyMiddleware;


$requirePolicy = $container->get(RequirePolicy::class);

$app->group('/admin', function ($group) use ($requirePolicy) {

    $group->get('/system/errors', [ErrorController::class, 'index'])
        ->setName('system.errors.index')
        ->add($requirePolicy('system.errors.view'));

    $group->get('/system/errors/{id}', [ErrorController::class, 'show'])
        ->setName('system.errors.show')
        ->add($requirePolicy('system.errors.view'));


    $group->get('/system/update',[UpdateController::class, 'index'])
        ->setName('system.update.index')
        ->add($requirePolicy('system.update.index'));

$group->post('/system/update/dry-run',[UpdateController::class, 'dryRun'])
        ->setName('system.update.dryrun')
        ->add($requirePolicy('system.update.dryrun'));


    $group->post('/system/errors/{id}/resolve', [ErrorController::class, 'resolve'])
        ->setName('system.errors.resolve')
        ->add($requirePolicy('system.errors.resolve'));
})
->add($container->get(CsrfMiddleware::class))
->add($container->get(AuthMiddleware::class));

?>