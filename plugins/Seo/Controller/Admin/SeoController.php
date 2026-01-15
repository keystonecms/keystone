<?php

namespace Keystone\Plugins\Seo\Controller\Admin;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Keystone\Core\Auth\Authorizer;
use Keystone\Domain\User\CurrentUser;

use Keystone\Plugins\Pages\Domain\PageService;


use Keystone\Plugins\Seo\Domain\{
    SeoService,
    SeoSubject,
    SeoMetadata
};
use Keystone\Http\Controllers\BaseController;

final class SeoController extends BaseController {
    public function __construct(
        private Twig $view,
        private SeoService $seoService,
        private PageService $pageService,
        private Authorizer $authorizer,
        private CurrentUser $currentUser
    ) { }

 public function edit(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

if (!$this->authorizer->allows($this->currentUser->user(), 'seo', 'edit', null)) {
    throw new ForbiddenException();
}


$page = $this->pageService->findById((int) $args['id']);

$subject = new SeoSubject(
            $args['type'],
            (int) $args['id']
        );

$seo = $this->seoService->getForSubject(
            subject: $subject,
            fallbackTitle: $page->title(),
            fallbackDescription: mb_substr(strip_tags($page->content()),0,160),
            fallbackSlug: $page->slug(),
            baseUrl: 'https://keystone-cms.lan'
    );

        return $this->view->render($response, '@seo/admin/edit.twig', [
            'subject' => $subject,
            'seo' => $seo,
            'page' => $page
        ]);
    }

    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {


    if (!$this->authorizer->allows($this->currentUser->user(), 'seo', 'edit', null)) {
    throw new ForbiddenException();
}

        $subject = new SeoSubject(
            $args['type'],
            (int) $args['id']
        );

        $data = $request->getParsedBody();

        $seo = new SeoMetadata(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            noIndex: isset($data['no_index']),
            canonical: $data['canonical'] ?: null,
            openGraph: [] // later uitbreidbaar
        );

        $this->seoService->update($subject, $seo);

   return $this->json($response, [
        'status'    => 'success',
        'message' => 'SEO details saved',

    ]);


    }
}

?>