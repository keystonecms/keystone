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
use Keystone\Http\Controllers\BaseController;
use Keystone\Security\CsrfToken;

use Keystone\Plugins\InternalLinks\Domain\InternalLinkService;
use Keystone\Plugins\InternalLinks\Domain\LinkSubject;

final class PageController extends BaseController {

    public function __construct(
        private PageService $pages,
        private CurrentUser $currentUser,
        private Authorizer $auth,
        private CsrfToken $token,
        private InternalLinkService $internalLinks,
        private Twig $view
    ) {}

public function schedule(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $data = $request->getParsedBody();

    $this->pages->schedulePublish(
        (int) $args['id'],
        (int) $data['version_id'],
        new \DateTimeImmutable($data['publish_at'])
    );

    return $this->json($response, [
        'status'  => 'success',
        'message' => 'Publish scheduled'
    ]);
}

public function publish(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = $request->getParsedBody();

        
        $page = $this->pages->findById((int) $args['id']);

        return $this->view->render(
            $response,
            '@pages/admin/publish.twig',
            [
                'page' => $page,
            ]
        );
    }

public function publishPost(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $data = $request->getParsedBody();

        $this->pages->publish((int) $args['id'], (int) $data['version_id']);
        return $response->withHeader('Location', '/admin/pages')->withStatus(302);
    }

public function unpublish(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->pages->unpublish((int) $args['id']);
        return $response->withHeader('Location', '/admin/pages')->withStatus(302);
    }

public function setHomepage(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->pages->setHomepage((int) $args['id']);
        return $response->withHeader('Location', '/admin/pages')->withStatus(302);
    }

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

    public function create(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {

            return $this->view->render(
            $response,
            '@pages/admin/create.twig'
            );
    }


    public function edit(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $page = isset($args['id'])



            ? $this->pages->findById((int) $args['id'])
            : null;

            $media = $this->pages->media((int) $args['id']);

            return $this->view->render(
            $response,
            '@pages/admin/edit.twig',
            [
                'page' => $page,
                'media' => $media
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

public function autosave(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $data = $request->getParsedBody();

    $versionId = $this->pages->autosave(
        (int) $args['id'],
        trim($data['title'] ?? ''),
        trim($data['slug'] ?? ''),
        $data['content'] ?? '',
        $data['template']
    );

    return $this->json($response, [
        'status'    => 'success',
        'versionId' => $versionId,
        'savedAt'   => date('H:i'),
        'csrfToken' => htmlspecialchars($this->token->generate(), ENT_QUOTES),
    ]);
}



public function save(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $data = $request->getParsedBody();

    try {
        $this->pages->save([
           'id' => (int) $args['id'],
           'title' =>  trim($data['title'] ?? ''),
           'slug' =>  trim($data['slug'] ?? ''),
           'status' => $data['status'] ?? 'draft',
           'authorId' => $this->currentUser->user()->id(),
           'template' => $data['template'],
           'content' => $data['content'] ?? ''
        ]);

    $this->internalLinks->syncLinksForSubject(
            new LinkSubject('page', (int) $args['id']),
            $data['internal_links'] ?? []
        );


        return $this->json($response, [
            'status'  => 'success',
            'message' => 'Page saved',
            'csrfToken' => htmlspecialchars($this->token->generate(), ENT_QUOTES),
        ]);

    } catch (\RuntimeException $e) {
        return $this->json($response, [
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


}
