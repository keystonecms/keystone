<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Auth;

use Keystone\Domain\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use Keystone\Http\Controllers\BaseController;

use Keystone\Plugins\Auth\Domain\Auth\TwoFactorService;


final class LoginController extends BaseController {

    public function __construct(
        private UserService $users,
        private Twig $view,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface  $logger,
        private TwoFactorService $twoFactor
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface
    {
 $queryParams = $request->getQueryParams();

        return $this->view->render(
            $this->responseFactory->createResponse(),
            'auth/login.twig',[
            
            'error' => isset($queryParams['error'])
        ]
        );
    }

public function authenticate(ServerRequestInterface $request): ResponseInterface {
    $data = (array) $request->getParsedBody();
    $response = new Response();

    $this->logger->info('Login attempt', [
            'email' => $data['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

    try {
        $user = $this->users->authenticate(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );

        // 🔐 Heeft user 2FA?
        if ($user->hasTwoFactor()) {

            $challengeToken = $this->twoFactor->startChallenge($user);

            $_SESSION['2fa_user_id'] = $user->id();

            return $this->json($response, [
                'status'   => 'success',
                'message'  => 'Tweestapsverificatie vereist',
                'redirect' => '/2fa?token=' . $challengeToken,
            ]);
        }

        // ✅ Geen 2FA → direct login
        $_SESSION['user_id'] = $user->id();

        return $this->json($response, [
            'status'   => 'success',
            'message'  => 'Succesvol ingelogd',
            'redirect' => '/admin/pages',
        ]);

    } catch (\RuntimeException $e) {
        return $this->json($response, [
            'status'  => 'error',
            'message' => 'Ongeldige login gegevens',
        ], 401);
        }
    }
}

?>