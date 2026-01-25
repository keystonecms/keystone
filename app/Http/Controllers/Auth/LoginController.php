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
use Keystone\Core\Auth\AuthorityActivityService;
use Keystone\Core\Auth\TwoFactor\TwoFactorHandlerInterface;
use Keystone\Core\Security\SecurityEventService;
use Keystone\Core\Auth\AuthService;
use Keystone\Auth\Event\UserLoggedIn;
use Keystone\Security\LoginAudit\LoginAuditService;

final class LoginController extends BaseController {

    public function __construct(
        private UserService $users,
        private Twig $view,
        private AuthService $auth,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface  $logger,
        private TwoFactorHandlerInterface $twoFactor,
        private AuthorityActivityService $authority,
        private SecurityEventService $security,
        private LoginAuditService $loginAudit
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface {
    
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

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    $this->logger->info('Login attempt', [
            'email' => $data['email'],
            'ip' => $ip,
        ]);

    try {
        $user = $this->users->authenticate(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );


    if ($user) {
        $this->loginAudit->log(
            $user->id(),
            $ip
        );
    }

    
if ($this->twoFactor->requiresTwoFactor($user)) {

    $challengeToken = $this->twoFactor->start($user);

    $_SESSION['2fa_user_id'] = $user->id();

    return $this->json($response, [
        'status'   => 'success',
        'message'  => 'Tweestapsverificatie vereist',
        'redirect' => '/2fa?token=' . $challengeToken,
    ]);
}

        // succesvol
        if (!$this->security->isNewIp($user->id(), $ip)) {
            $this->security->record(
                $user->id(),
                'login_new_ip',
                $ip
            );
        }

        $this->security->record(
            $user->id(),
            'login_success',
            $ip
        );

        $this->auth->login($user);

        $this->authority->loginSuccesFull($data['email'], $ip);

        return $this->json($response, [
            'status'   => 'success',
            'message'  => 'Succesvol ingelogd',
            'redirect' => '/admin/dashboard',
        ]);

    } catch (\RuntimeException $e) {

            if ($user) {
                $this->security->record(
                    $user->id(),
                    'login_failed',
                    $ip
                );

                if (
                    $this->security
                        ->failedAttemptsExceeded($user->id(), 5, 5) >= 3
                ) {
                    $this->security->record(
                        $user->id(),
                        'login_failed_threshold',
                        $ip
                );
        }
    }

    $this->authority->loginFailed($data['email'], $ip);

        return $this->json($response, [
            'status'  => 'error',
            'message' => 'Ongeldige login gegevens',
        ], 401);
        }
    }
}

?>