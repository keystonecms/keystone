<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Controller\Public;

use Keystone\Plugins\Auth\Domain\Auth\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class RegisterController {
    
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
            '@auth/frontend/register.twig'
        );
    }

    public function submit(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $email = (string) ($data['email'] ?? '');

        $this->logger->info('Registration attempt', [
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // Business logic zit volledig in AuthService
        $this->auth->register($email);

        // Altijd dezelfde response (anti user enumeration)
        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/register_success.twig'
        );
    }
}

?>
