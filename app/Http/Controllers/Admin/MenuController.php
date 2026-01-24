<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Domain\Menu\Repository\MenuRepositoryInterface;
use Keystone\Domain\Menu\Service\MenuCommandService;
use Keystone\Domain\Menu\Service\LinkResolver;
use Slim\Views\Twig;

final class MenuController {


public function __construct(
        private Twig $view,
        private MenuRepositoryInterface $menus,
        private MenuCommandService $commands,
        private LinkResolver $linkResolver
    ) {
    }

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->view->render($response, '@core/admin/menus/index.twig', [
            'menus' => $this->menus->getAll(),
        ]);
    }

    public function create(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->view->render($response, 'admin/menus/create.twig');
    }

    public function store(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = $request->getParsedBody();

        $this->commands->createMenu(
            $data['name'],
            $data['handle'],
            $data['description'] ?? null
        );

        return $response
            ->withHeader('Location', '/admin/menus')
            ->withStatus(302);
    }

    public function edit(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $menu = $this->menus->getById((int) $args['id']);

        if ($menu === null) {
            return $response->withStatus(404);
        }

        return $this->view->render($response, 'admin/menus/edit.twig', [
            'menu' => $menu,
            'linkResolver' => $this->linkResolver,
        ]);
    }

    public function storeItem(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = $request->getParsedBody();

$this->commands->addItemToMenu(
    menuId: (int) $args['id'],
    parentId: $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
    label: $data['label'],
    linkType: $data['link_type'],
    linkTarget: $data['link_target'],
    cssClass: $data['css_class'] ?: null,
    target: $data['target'] ?: null
);


        return $response
            ->withHeader('Location', '/admin/menus/' . $args['id'])
            ->withStatus(302);
    }

    public function updateItem(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $data = $request->getParsedBody();

$this->commands->updateMenuItem(
    id: (int) $args['id'],
    label: $data['label'],
    // linkType: $data['link_type'],
    // linkTarget: $data['link_target'],
    isVisible: isset($data['is_visible']),
    cssClass: $data['css_class'] ?: null,
    target: $data['target'] ?: null
);


        return $response
            ->withHeader('Location', $request->getHeaderLine('Referer'))
            ->withStatus(302);
    }

    public function deleteItem(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->commands->removeMenuItem((int) $args['id']);

        return $response
            ->withHeader('Location', $request->getHeaderLine('Referer'))
            ->withStatus(302);
    }
}


?>