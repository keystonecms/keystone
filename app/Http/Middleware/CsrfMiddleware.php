<?php

declare(strict_types=1);

namespace Keystone\Http\Middleware;

use Keystone\Security\CsrfToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CsrfMiddleware implements MiddlewareInterface {
    public function __construct(
        private CsrfToken $csrf,
        private ResponseFactoryInterface $responseFactory
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {

            $data = (array) $request->getParsedBody();
            $token = $data['_csrf_token'] ?? null;

            if (!$this->csrf->validate($token)) {
                return $this->forbidden();
            }
        }

        return $handler->handle($request);
    }

private function forbidden(): never
{
    throw new \Keystone\Core\Http\Exception\ForbiddenException(
        'Invalid CSRF token'
    );
}

}

?>
