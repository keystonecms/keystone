<?php

namespace Keystone\Http\Middleware;

use Keystone\Core\Setup\SetupConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class InstallerGuardMiddleware implements MiddlewareInterface {

public function __construct(
        private SetupConfig $config
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // installer afgerond → alles normaal

        if (file_exists($this->config->lockFilePath)) {
            return $handler->handle($request);
        }

        $path = $request->getUri()->getPath();

        // sta installer zelf toe
        if (str_starts_with($path, '/installer')) {
            return $handler->handle($request);
        }

        // sta assets toe (optioneel aanpassen)
        if (
            str_starts_with($path, '/assets') ||
            str_starts_with($path, '/css') ||
            str_starts_with($path, '/js')
        ) {
            return $handler->handle($request);
        }

        // alles anders → installer
        $response = new Response();
        return $response
            ->withHeader('Location', '/installer')
            ->withStatus(302);
    }
}


?>