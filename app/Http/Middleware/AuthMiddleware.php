<?php

declare(strict_types=1);

namespace Keystone\Http\Middleware;

use Keystone\Domain\User\CurrentUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CurrentUser $currentUser
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {


        // Als user NIET is ingelogd → redirect naar login
        if (!$this->currentUser->isAuthenticated()) {

            $response = new Response();

            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        // User is ingelogd → request mag door
        return $handler->handle($request);
    }
}

?>