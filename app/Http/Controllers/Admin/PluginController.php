<?php

namespace Keystone\Http\Controllers\Admin;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Plugin\PluginRegistry;

final class PluginController
{
    public function __construct(
        private PluginRegistry $registry,
        private Twig $twig
    ) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->twig->render(
            $response,
            'admin/plugins/index.twig',
            [
                'plugins' => $this->registry->all()
            ]
        );
    }
}


?>
