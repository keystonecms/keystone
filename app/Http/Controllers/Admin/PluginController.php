<?php

namespace Keystone\Http\Controllers\Admin;

use Keystone\Core\Plugin\PluginService;
use Keystone\Core\Plugin\PluginInstallerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

use Keystone\Http\Controllers\BaseController;

final class PluginController extends BaseController {
    public function __construct(
        private PluginService $plugins,
        private PluginInstallerService $pluginInstaller,
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

public function install(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {
    try {
        $this->pluginInstaller->install($args['name']);

    } catch (\Throwable $e) {
   return $this->json($response, [
        'status'  => 'error',
        'message' => $e->getMessage()
        ]);
    }

    return $this->json($response, [
        'status'  => 'success',
        'message' => 'Plugin ' . $args['name'] . ' succesvol geinstalleerd.'
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