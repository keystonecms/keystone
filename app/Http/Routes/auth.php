<?php

use Keystone\Http\Controllers\Auth\LoginController;
use Keystone\Http\Middleware\CsrfMiddleware;

$app->group('/login', function ($group) {
    $group->get('', LoginController::class . ':show')->setName('login.show');
    $group->post('', LoginController::class . ':authenticate');
})->add($container->get(CsrfMiddleware::class));

$app->get('/logout', function () use ($container) {
    session_destroy();

    return $container
        ->get(Psr\Http\Message\ResponseFactoryInterface::class)
        ->createResponse(302)
        ->withHeader('Location', '/login');
});

?>