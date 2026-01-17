<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Account;

use Keystone\Http\Controllers\BaseController;
use Keystone\Domain\User\CurrentUser;
use Keystone\Domain\User\UserService;
use Keystone\Http\Exception\ForbiddenException;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class AccountController extends BaseController
{
    public function __construct(
        private CurrentUser $currentUser,
        private UserService $users,
        private Twig $twig
    ) {}

    /**
     * GET /account
     */
    public function index(): ResponseInterface
    {
        $user = $this->currentUser->user();

        return $this->twig->render(
            new Response(),
            '@auth/account/index.twig',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * POST /account/password (AJAX)
     */
    public function changePassword(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->currentUser->user();
        $data = (array) $request->getParsedBody();

        if (
            empty($data['current_password']) ||
            empty($data['new_password'])
        ) {
            return $this->json(new Response(), [
                'status'  => 'error',
                'message' => 'Alle velden zijn verplicht',
            ], 422);
        }

        try {
            $this->users->changePassword(
                $user->id(),
                $data['current_password'],
                $data['new_password']
            );
        } catch (\RuntimeException $e) {
            return $this->json(new Response(), [
                'status'  => 'error',
                'message' => 'Huidig wachtwoord is onjuist',
            ], 403);
        }

        return $this->json(new Response(), [
            'status'  => 'success',
            'message' => 'Wachtwoord gewijzigd',
        ]);
    }

    /**
     * POST /account/2fa/disable (AJAX)
     */
    public function disableTwoFactor(): ResponseInterface
    {
        $user = $this->currentUser->user();

        $this->users->setTwoFactorSecret($user->id(), null);

        return $this->json(new Response(), [
            'status'  => 'success',
            'message' => '2FA is uitgeschakeld',
        ]);
    }
}
