<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Controller\Public;

use Keystone\Domain\User\UserService;
use Keystone\Plugins\Auth\Domain\Auth\TwoFactorService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Slim\Psr7\Response;
use Keystone\Http\Controllers\BaseController;
use Keystone\Plugins\Auth\Domain\Token\InvalidTokenException;
use Keystone\Domain\User\UserRepositoryInterface;

final class TwoFactorController extends BaseController {

    public function __construct(
        private TwoFactorService $twoFactor,
        private UserService $users,
        private UserRepositoryInterface $userRepository,
        private Twig $view
    ) {}

    /**
     * Toon 2FA invoerformulier
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();

        $token = $query['token'] ?? null;

        if (! $token) {
            return new Response(400);
        }

        return $this->view->render(
            new Response(),
            '@auth/frontend/2fa_login.twig',
            [
                'token' => $token,
            ]
        );
    }

    /**
     * Verifieer 2FA code
     */
public function verify(ServerRequestInterface $request): ResponseInterface
{
    $data = (array) $request->getParsedBody();
    $response = new Response();

    $userId = $_SESSION['2fa_user_id'] ?? null;

    if (! $userId) {
        return $this->json($response, [
            'status'  => 'error',
            'message' => '2FA sessie verlopen, log opnieuw in',
            'redirect'=> '/login',
        ], 403);
    }

    $user = $this->userRepository->findById($userId);

    try {
        $this->twoFactor->verify(
            $data['token'] ?? '',
            $data['code'] ?? '',
            $user->twoFactorSecret()
        );

        unset($_SESSION['2fa_user_id']);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;

        return $this->json($response, [
            'status'   => 'success',
            'message'  => 'Verificatie geslaagd',
            'redirect' => '/admin/pages',
        ]);

    } catch (TwoFactorFailedException $e) {
        return $this->json($response, [
            'status'  => 'error',
            'message' => 'Ongeldige verificatiecode',
        ], 422);

    } catch (InvalidTokenException $e) {
        session_destroy();

        return $this->json($response, [
            'status'   => 'error',
            'message'  => 'Verificatie verlopen, log opnieuw in',
            'redirect' => '/login',
        ], 403);
    }
}

}

?>