<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Controller\Public;

use Keystone\Plugins\Auth\Domain\Auth\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class ForgotPasswordController
{
    public function __construct(
        private AuthService $auth,
        private Twig $view,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/forgot_password.twig'
        );
    }

    public function submit(ServerRequestInterface $request): ResponseInterface
    {
        $data  = (array) $request->getParsedBody();
        $email = (string) ($data['email'] ?? '');

        $this->logger->info('Password reset requested', [
            'email' => $email,
            'ip'    => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // Altijd zwijgend (anti user enumeration)
        $this->auth->requestPasswordReset($email);

        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/forgot_password_success.twig'
        );
    }
}


?>