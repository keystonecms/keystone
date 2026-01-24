<?php

namespace Keystone\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Keystone\Core\Auth\PolicyResolver;
use Keystone\Domain\User\CurrentUser;
use Psr\Log\LoggerInterface;


final class PolicyMiddleware {


    public function __construct(
        private PolicyResolver $policies,
        private CurrentUser $currentUser,
        private LoggerInterface $logger,
        private string $requiredPolicy
    ) {}

    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

    $this->logger->debug('Policy check', [
    'user'   => $this->currentUser->id(),
    'policy' => $this->requiredPolicy,
]);


        if (!$this->currentUser->isAuthenticated()) {
            // redirect / json error
        }

        if (!$this->policies->userHasPolicy(
            $this->currentUser->id(),
            $this->requiredPolicy
        )) {
            // redirect / json error
        }

        return $handler->handle($request);
    }
}

?>