<?php

declare(strict_types=1);

namespace Keystone\Plugins\Pages\Controller\Public;

use Keystone\Plugins\Pages\Domain\PageService;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;


use Keystone\Plugins\Seo\Domain\SeoSubject;
use Keystone\Plugins\Seo\Domain\SeoService;

final class PageController {
    public function __construct(
        private SeoService $seoService,
        private PageService $pages,
        private Twig $view
    ) {}

    public function homepage(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $page = $this->pages->getHomepage();

        $seo = $this->seoService->getForSubject(
    subject: new SeoSubject('page', $page->id()),
    fallbackTitle: $page->title(),
    fallbackDescription: mb_substr(strip_tags($page->content()), 0, 160),
    fallbackSlug: $page->slug(),
    baseUrl: 'https://keystone-cms/lan'
);


        return $this->view->render(
            $response,
            '@pages/frontend/home.twig',
            [
            'page' => $page,
            'seo' => $seo ]
        );
    }


    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

    $page = $this->pages->findBySlug($args['slug']);

        if (!$page) {
            throw new HttpNotFoundException($request);
        }

$seo = $this->seoService->getForSubject(
    subject: new SeoSubject('page', $page->id()),
    fallbackTitle: $page->title(),
    fallbackDescription: mb_substr(strip_tags($page->content()), 0, 160),
    fallbackSlug: $page->slug(),
    baseUrl: 'https://keystone-cms/lan'
);


$resolvedLinks = [];

foreach ($links as $link) {
    if ($link->to()->type() === 'page') {
        $targetPage = $this->pageService->getById(
            $link->to()->id()
        );

        $resolvedLinks[] = [
            'url' => $this->basePath . '/' . $targetPage->slug(),
            'anchor' => $link->anchorText(),
            'nofollow' => $link->nofollow(),
        ];
    }
}




        return $this->view->render(
            $response,
            '@pages/frontend/show.twig',
            [
                'page' => $page,
                'seo'  => $seo,
            ]
        );
}


private function InternalLinks(int $page_id) {

    $subject = new LinkSubject('page', $page_id);

    return $this->internalLinkService->getLinksFrom($subject);
    }
}
?>