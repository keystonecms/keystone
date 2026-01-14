<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Auth;

use Keystone\Domain\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class LoginController {
    public function __construct(
        private UserService $users,
        private Twig $view,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface  $logger
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface
    {
 $queryParams = $request->getQueryParams();

error_log('TWIG HASH: ' . spl_object_id($this->view));

        return $this->view->render(
            $this->responseFactory->createResponse(),
            'auth/login.twig',[
            
            'error' => isset($queryParams['error'])
        ]
        );
    }

    public function authenticate(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

    $this->logger->info('Login attempt', [
            'email' => $data['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        try {
            $user = $this->users->authenticate(
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

      $_SESSION['user_id'] = $user->id();

            $this->logger->warning('Login succesfull', [
            'email' => $data['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

 
            return $this->redirect('/admin/pages');

        } catch (\RuntimeException $e) {
            return $this->redirect('/login?error=1');
        }
    }

    private function redirect(string $path): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $path);
    }
}
