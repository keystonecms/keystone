<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Controller\Public;

use Keystone\Plugins\Pages\Infrastructure\Persistence\PageRepository;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Http\Exception\NotFoundException;

final class PageController
{
    public function __construct(
        private PageRepository $pages,
        private Twig $view
    ) {}

    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

    $page = $this->pages->findBySlug($args['slug']);


        if (!$page) {
            throw new NotFoundException();
        }

        return $this->view->render(
            $response,
            '@pages/frontend/show.twig',
            [
                'page' => $page,
            ]
        );
    }
}
?>