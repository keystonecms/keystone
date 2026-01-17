<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Controller\Admin;

use Keystone\Http\Controllers\BaseController;
use Keystone\Domain\User\UserService;
use Keystone\Core\Auth\Authorizer;
use Keystone\Domain\User\CurrentUser;
use Keystone\Http\Exception\ForbiddenException;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class AdminUsersController extends BaseController
{
    public function __construct(
        private UserService $users,
        private Authorizer $auth,
        private CurrentUser $currentUser,
        private Twig $twig
    ) {}

    /**
     * GET /admin/users
     * HTML render (geen AJAX)
     */
    public function index(): ResponseInterface
    {
        $user = $this->currentUser->user();

        if (!$this->auth->allows($user, 'auth', 'manage-users')) {
            throw new ForbiddenException();
        }

        return $this->twig->render(
            new Response(),
            '@auth/admin/users/index.twig',
            [
                'users' => $this->users->all(),
            ]
        );
    }

    /**
     * POST /admin/users/status
     * AJAX only
     */
    public function updateStatus(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->currentUser->user();

        if (!$this->auth->allows($user, 'auth', 'manage-users')) {
            throw new ForbiddenException();
        }

        $data = (array) $request->getParsedBody();

        $this->users->changeStatus(
            (int) $data['user_id'],
            (string) $data['status']
        );

        return $this->json(new Response(), [
            'status'  => 'success',
            'message' => 'Gebruikersstatus bijgewerkt',
        ]);
    }
}


?>