<?php

namespace Keystone\Plugins\System\Controller\Admin;

use Keystone\Infrastructure\Update\UpdaterService;
use Keystone\Core\Auth\Authorizer;
use Keystone\Http\Controllers\BaseController;
use Keystone\Domain\User\CurrentUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Slim\Psr7\Response;


final class UpdateController extends BaseController {
    
    public function __construct(
        private Twig $twig,
        private UpdaterService $updater,
        private CurrentUser $currentUser,
        private Authorizer $auth
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface {

    $user = $this->currentUser->user();
       
             if (!$this->auth->allows($user, 'system', 'update')) {
            throw new ForbiddenException();
            }

        return $this->twig->render(
            new Response(),
            '@system/admin/update/index.twig');
    
        }

public function dryRun(ServerRequestInterface $request): ResponseInterface
{
    $user = $this->currentUser->user();

    if (!$this->auth->allows($user, 'system', 'update')) {
            throw new ForbiddenException();
        }

    $zipUrl = $request->getParsedBody()['zip'] ?? null;

    $zipPath = $this->downloader->download($zipUrl);

    $result = $this->updater->dryRun($zipPath);

    return $this->json([
        'status' => $result->isOk() ? 'success' : 'error',
        'checks' => $result->all(),
        'zip'    => $zipPath
    ]);
}


public function activate(ServerRequestInterface $request): ResponseInterface
{
    $user = $this->currentUser->user();

    if (!$this->auth->allows($user, 'system', 'update')) {
            throw new ForbiddenException();
        }

    $data = $request->getParsedBody();

    $this->updater->activate(
        $data['zip'],
        $data['version'],
        dirname(BASE_PATH)
    );

    return $this->json([
        'status' => 'success',
        'message' => 'Update succesvol geactiveerd',
        'redirect' => '/admin'
    ]);
}

}


?>