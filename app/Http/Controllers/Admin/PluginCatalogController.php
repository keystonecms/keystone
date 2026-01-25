<?php

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

use Keystone\Core\Plugin\Catalog\PluginCatalogService;
use Keystone\Core\Plugin\PluginRegistryInterface;



final class PluginCatalogController {
    
    public function __construct(
        private PluginCatalogService $catalog,
        private PluginRegistryInterface $plugins,
        private Twig $view
    ) {}

public function index(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    $catalog   = $this->catalog->fetch();
    $installed = $this->plugins->allIndexedByPackage();

    foreach ($catalog as &$plugin) {
        if (isset($installed[$plugin['slug']])) {
            $plugin['installed'] = true;
            $plugin['hasUpdate'] = version_compare(
                $plugin['version'],
                $installed[$plugin['slug']]['version'],
                '>'
            );
        }
    }

    return $this->view->render(
        $response,
        '@core/admin/plugins/catalog.twig',
        [
            'catalog'   => $catalog,
            'installed' => $installed,
            ]
        );
    }
}

?>
