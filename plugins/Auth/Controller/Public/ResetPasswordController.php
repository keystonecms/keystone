<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Controller\Public;

use Keystone\Plugins\Auth\Domain\Auth\AuthService;
use Keystone\Plugins\Auth\Domain\Token\InvalidTokenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class ResetPasswordController
{
    public function __construct(
        private AuthService $auth,
        private Twig $view,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger
    ) {}

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $token = (string) ($query['token'] ?? '');

        if ($token === '') {
            return $this->invalid();
        }

        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/reset_password.twig',
            [
                'token' => $token,
            ]
        );
    }

    public function submit(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        $token    = (string) ($data['token'] ?? '');
        $password = (string) ($data['password'] ?? '');

        try {
            $this->auth->resetPassword($token, $password);

            $this->logger->info('Password reset successful', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            return $this->view->render(
                $this->responseFactory->createResponse(),
                '@auth/frontend/reset_password_success.twig'
            );

        } catch (InvalidTokenException $e) {
            $this->logger->warning('Invalid password reset token', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            return $this->invalid();
        }
    }

    private function invalid(): ResponseInterface
    {
        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/reset_password_invalid.twig'
        );
    }
}


?>