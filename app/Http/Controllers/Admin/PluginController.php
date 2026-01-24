<?php

namespace Keystone\Http\Controllers\Admin;

use Keystone\Core\Plugin\PluginService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class PluginController {
    public function __construct(
        private PluginService $plugins,
        private Twig $view
    ) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {


        return $this->view->render($response, 'admin/plugins/index.twig', [
            'plugins' => $this->plugins->listPlugins(),
        ]);
    }

    public function enable(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->plugins->enable($args['name']);


    return $response
        ->withHeader('Location', '/admin/plugins')
        ->withStatus(302);
    }

    public function disable(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->plugins->disable($args['name']);

        return $response
        ->withHeader('Location', '/admin/plugins')
        ->withStatus(302);
    }
}

?>