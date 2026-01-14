<?php

namespace Keystone\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Keystone\Domain\User\CurrentUser;
use Keystone\Domain\User\UserRepositoryInterface;

final class CurrentUserMiddleware implements MiddlewareInterface {

    public function __construct(
        private UserRepositoryInterface $users,
        private CurrentUser $currentUser
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {


 if (isset($_SESSION['user_id'])) {

    $user = $this->users->findById((int) $_SESSION['user_id']);

    if ($user) {
        $this->currentUser->set($user);
    }
}


        return $handler->handle($request);
    }
}

?>