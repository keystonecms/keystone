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

final class ActivateController {


    public function __construct(
        private AuthService $auth,
        private Twig $view,
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $token = (string) ($query['token'] ?? '');

        if ($token === '') {
            return $this->invalid();
        }

        try {
            $this->auth->activate($token);

            $this->logger->info('Account activated', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            return $this->view->render(
                $this->responseFactory->createResponse(),
                '@auth/frontend/activate_success.twig'
            );

        } catch (InvalidTokenException $e) {
            $this->logger->warning('Invalid activation token', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            return $this->invalid();
        }
    }

    private function invalid(): ResponseInterface
    {
        return $this->view->render(
            $this->responseFactory->createResponse(),
            '@auth/frontend/activate_invalid.twig'
        );
    }
}

?>