<?php

declare(strict_types=1);

namespace Keystone\Http\Middleware;

use Keystone\Admin\Menu\AdminMenuRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;
use Twig\Environment;

final class AdminMenuMiddleware implements MiddlewareInterface {
    public function __construct(
        private AdminMenuRegistry $menu,
        private Environment $twig
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

if ($request === null) {
    throw new \RuntimeException('REQUEST IS NULL');
}

        $routeContext = RouteContext::fromRequest($request);
        $route        = $routeContext->getRoute();
        $currentRoute = $route?->getName();

        $this->twig->addGlobal(
            'admin_menu',
            $this->menu->all($currentRoute)
        );
;
        return $handler->handle($request);
    }
}


?>