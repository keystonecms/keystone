<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Controller\Admin;

use Keystone\Core\Auth\Authorizer;
use Keystone\Domain\User\CurrentUser;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Http\Exception\ForbiddenException;
use Keystone\Plugins\Pages\Domain\PageService;

final class PageController {

    public function __construct(
        private PageService $pages,
        private CurrentUser $currentUser,
        private Authorizer $auth,
        private Twig $view
    ) {}

    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $user = $this->currentUser->user();

        if (!$this->auth->allows($user, 'pages', 'view')) {
            throw new ForbiddenException();
        }

        return $this->view->render(
            $response,
            '@pages/admin/index.twig',
            [
                'pages' => $this->pages->all(),
            ]
        );
    }

    public function form(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $page = isset($args['id'])


        
            ? $this->pages->findById((int) $args['id'])
            : null;

            return $this->view->render(
            $response,
            '@pages/admin/form.twig',
            [
                'page' => $page,
            ]
        );
    }

public function delete(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $id = (int) $args['id'];

    $this->pages->delete($id);

    return $response
        ->withHeader('Location', '/admin/pages')
        ->withStatus(302);
}


    public function save(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = $request->getParsedBody();

        $this->pages->save([
            'id'        => $data['id'] ?? null,
            'title'     => $data['title'],
            'slug'      => $data['slug'],
            'content'   => $data['content'],
            'published' => isset($data['published']) ? 1 : 0,
        ]);

        return $response
            ->withHeader('Location', '/admin/pages')
            ->withStatus(302);
    }
}
