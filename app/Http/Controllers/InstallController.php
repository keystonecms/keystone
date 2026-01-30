<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers;

use Keystone\Core\Setup\InstallerKernel;
use Keystone\Core\Setup\InstallerState;
use Keystone\Core\Setup\InstallerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Keystone\Http\Controllers\BaseController;
use Keystone\Core\Setup\SetupConfig;
use Keystone\Core\Setup\InstallerCommit;
use Keystone\Infrastructure\AppUrlDetector;

final class InstallController extends BaseController {


public function __construct(
        private InstallerKernel $installerKernel,
        private Twig $view,
        private SetupConfig $config,
        private InstallerCommit $commit
    ) {}

public function index(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    return $this->view->render($response, 'index.twig');
}


public function commit(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {

    $appUrl = AppUrlDetector::detect($request);


    $this->commit->handle(appUrl: $appUrl);

    $payload = json_encode([
        'status'   => 'ok',
        'redirect' => '/login',
    ], JSON_THROW_ON_ERROR);

    $response->getBody()->write($payload);

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
}

public function step(
    ServerRequestInterface $request,
    ResponseInterface $response,
    array $args
): ResponseInterface {

    $stepIndex = (int) $args['step'];
    $step = $this->installerKernel->getStepByIndex($stepIndex);

    $state = $_SESSION['installer_state'] ?? null;

    $vars = [];

    if ($stepIndex === 5 && $state instanceof InstallerState) {
        $vars['generatedAdminPassword'] = $state->generatedAdminPassword;
        $vars['adminEmail'] = $state->adminEmail;
    }

    if (!$step) {
        return $response->withStatus(404);
    }

    $html = $this->view->fetch('step-' . $stepIndex . '.twig', ['vars' => $vars ]);

    $response->getBody()->write(json_encode([
        'html' => $html,
        'meta' => [
            'step' => $step->getName(),
            'title' => $step->getTitle(),
            'description' => $step->getDescription(),
        ],
    ]));

    return $response->withHeader('Content-Type', 'application/json');
}



public function run(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    $data = json_decode((string) $request->getBody(), true);

    if (!is_array($data) || !isset($data['step'])) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'errors' => ['Invalid request payload'],
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    try {
        $result = $this->installerKernel->runStep(
            $data['step'],
            $data['payload'] ?? []
        );

            $response->getBody()->write(json_encode(array_merge(
                ['status' => 'ok'],
                $result
            )));
    } catch (InstallerException $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'errors' => $e->getErrors(),
        ]));
    }

    return $response->withHeader('Content-Type', 'application/json');
    }

}

?>
