<?php

declare(strict_types=1);

namespace Keystone\Http\Controller;

use Keystone\Core\Setup\InstallerKernel;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\Exception\EnvironmentCheckFailed;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Keystone\Http\Controller\BaseController;

final class InstallController extends BaseController {


public function __construct(
        private InstallerKernel $installer,
        private Twig $view
    ) {}

    public function index(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'installer/index.twig');
    }

    public function check(Request $request, Response $response): Response
    {
        try {
            $this->installer->run(new InstallerState());

            return $this->json($response, [
                'success' => true,
                'next' => 'database',
            ]);
        } catch (EnvironmentCheckFailed $e) {
            return $this->json($response, [
                'success' => false,
                'errors' => explode("\n", $e->getMessage()),
            ], 422);
        }
    }

    public function database(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $state = new InstallerState(
                dbHost: $data['db_host'],
                dbName: $data['db_name'],
                dbUser: $data['db_user'],
                dbPass: $data['db_pass'],
            );

            $this->installer->run($state);

            return $this->json($response, [
                'success' => true,
                'next' => 'admin',
            ]);
        } catch (\Throwable $e) {
            return $this->json($response, [
                'success' => false,
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function admin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $state = new InstallerState(
                adminEmail: $data['email'],
                adminPassword: $data['password'],
            );

            $this->installer->run($state);

            return $this->json($response, [
                'success' => true,
                'redirect' => '/',
            ]);
        } catch (\Throwable $e) {
            return $this->json($response, [
                'success' => false,
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    private function json(Response $response, array $payload, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
