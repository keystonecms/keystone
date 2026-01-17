<?php

declare(strict_types=1);

namespace Keystone\Http\Error;

use Keystone\Http\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
use Throwable;

final class ErrorHandler {
    public function __construct(
        private Twig $twig,
        private LoggerInterface $logger,
        private ResponseFactoryInterface $responseFactory
    ) {}

    /**
     * Slim 4 default error handler
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {

        // 404 Not Found
        if ($exception instanceof HttpNotFoundException) {
            return $this->notFound($request);
        }

        // 403 Forbidden
        if ($exception instanceof ForbiddenException) {
            return $this->forbidden($request);
        }

        // 500 Internal Server Error (fallback)
        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
            'path'      => (string) $request->getUri(),
        ]);

        return $this->internalError($request, $exception, $displayErrorDetails);
    }

    // ─────────────────────────────────────────────────────────────
    // Error types
    // ─────────────────────────────────────────────────────────────

    private function notFound(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info('Route not found', [
            'path' => (string) $request->getUri(),
        ]);

        if ($this->isAjax($request)) {
            return $this->json(
                404,
                'Pagina niet gevonden'
            );
        }

        return $this->render(
            404,
            'E404',
            'Pagina niet gevonden.'
        );
    }

    private function forbidden(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->warning('Forbidden', [
            'path' => (string) $request->getUri(),
        ]);

        if ($this->isAjax($request)) {
            return $this->json(
                403,
                'Geen toegang'
            );
        }

        return $this->render(
            403,
            'E403',
            'Je hebt geen rechten om deze actie uit te voeren.'
        );
    }

private function internalError(
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails
): ResponseInterface {

    // Unieke foutcode
    $errorId = $this->generateErrorId();

    // Log ALLES met dezelfde code
    $this->logger->error(
        sprintf('Internal error [%s]: %s', $errorId, $exception->getMessage()),
        [
            'error_id' => $errorId,
            'exception' => $exception,
            'path' => (string) $request->getUri(),
            'method' => $request->getMethod(),
        ]
    );

    // AJAX → JSON
    if ($this->isAjax($request)) {
        return $this->json(
            500,
            sprintf(
                'Er ging iets mis. Referentie: %s',
                $errorId
            )
        );
    }

    // HTML → error page
    return $this->render(
        500,
        $errorId,
        $displayErrorDetails
            ? $exception->getMessage()
            : 'Er ging iets mis. Neem contact op met support.'
    );
}


    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function render(
        int $status,
        string $code,
        string $message
    ): ResponseInterface {
        return $this->twig->render(
            $this->responseFactory->createResponse($status),
            'errors/error.twig',
            [
                'status'  => $status,
                'code'    => $code,
                'message' => $message,
            ]
        );
    }

    private function json(
        int $status,
        string $message
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $message,
        ]));

        return $response->withHeader(
            'Content-Type',
            'application/json'
        );
    }

    private function isAjax(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest'
            || str_contains(
                $request->getHeaderLine('Accept'),
                'application/json'
            );
    }
    private function generateErrorId(): string {
    return 'ERR-' . strtoupper(bin2hex(random_bytes(8)));
    }

}

?>