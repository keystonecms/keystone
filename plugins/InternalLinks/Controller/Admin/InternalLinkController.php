<?php

declare(strict_types=1);

namespace Keystone\Plugins\InternalLinks\Controller\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\Auth\Authorizer;
use Keystone\Plugins\InternalLinks\Domain\{
    InternalLinkService,
    LinkSubject,
    InternalLink
};
use Slim\Psr7\Response;
use Keystone\Domain\User\CurrentUser;
use Slim\Views\Twig;


final class InternalLinkController {
    public function __construct(
        private Twig $view,
        private InternalLinkService $service,
        private Authorizer $authorizer,
        private CurrentUser $currentUser
    ) {}

   public function index(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->authorizer->allows(
            $this->currentUser->user(),
            'internal_links',
            'view',
            null
        );

        $subject = new LinkSubject(
            $args['type'],
            (int) $args['id']
        );

        $links = $this->service->getLinksFrom($subject);


        return $this->view->render($response, '@internal-links/admin/index.twig', [
            'subject' => $subject,
            'links' => $links,
            'page' => $subject
        ]);
    }

    public function store(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->authorizer->allows(
            auth()->user(),
            'internal_links',
            'edit',
            null
        );

        $data = $request->getParsedBody();

        $from = new LinkSubject(
            $args['type'],
            (int) $args['id']
        );

        $to = new LinkSubject(
            $data['to_type'],
            (int) $data['to_id']
        );

        $this->service->addLink(
            $from,
            $to,
            $data['anchor_text'] ?? '',
            isset($data['nofollow'])
        );

        return $response
            ->withHeader(
                'Location',
                '/admin/internal-links/' . $args['type'] . '/' . $args['id']
            )
            ->withStatus(302);
    }

    public function delete(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->authorizer->allows(
            auth()->user(),
            'internal_links',
            'edit',
            null
        );

        $data = $request->getParsedBody();

        $link = new InternalLink(
            from: new LinkSubject($args['type'], (int) $args['id']),
            to: new LinkSubject($data['to_type'], (int) $data['to_id']),
            anchorText: $data['anchor_text'],
            nofollow: (bool) ($data['nofollow'] ?? false)
        );

        $this->service->removeLink($link);

        return $response
            ->withHeader(
                'Location',
                '/admin/internal-links/' . $args['type'] . '/' . $args['id']
            )
            ->withStatus(302);
    }
}


?>