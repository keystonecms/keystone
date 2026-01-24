<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Admin;

use Keystone\Core\System\ErrorRepositoryInterface;
use Keystone\Domain\User\CurrentUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class ErrorController {
    public function __construct(
        private Twig $view,
        private ErrorRepositoryInterface $errors,
        private CurrentUser $currentUser
    ) {}

    /**
     * GET /admin/system/errors
     */
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->view->render($response, 'admin/system/errors/index.twig', [
            'errors' => $this->errors->findUnresolved(),
            'stats'  => $this->errors->stats(),
        ]);
    }

    /**
     * GET /admin/system/errors/{id}
     */
    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        return $this->view->render($response, 'admin/system/errors/show.twig', [
            'error' => $this->errors->find((int) $args['id']),
        ]);
    }

    /**
     * POST /admin/system/errors/{id}/resolve
     */
    public function resolve(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $this->errors->markResolved(
            (int) $args['id'],
            $this->currentUser->id()
        );

        return $response
            ->withHeader('Location', '/admin/system/errors')
            ->withStatus(302);
    }
}

?>