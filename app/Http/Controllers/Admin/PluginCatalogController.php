<?php

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

use Keystone\Core\Plugin\Catalog\PluginCatalogService;
use Keystone\Core\Plugin\PluginRepository;



final class PluginCatalogController {
    public function __construct(
        private PluginCatalogService $catalog,
        private PluginRepository $plugins,
        private Twig $view
    ) {}

    public function index(
           ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {


        return $this->view->render($response,
            '@core/admin/plugins/catalog.twig',
            [
                'catalog'   => $this->catalog->fetch(),
                'installed' => $this->plugins->allIndexedByPackage(),
            ]
        );
    }
}

?>
