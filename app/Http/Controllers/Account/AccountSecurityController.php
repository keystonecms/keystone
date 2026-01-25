<?php

namespace Keystone\Http\Controllers\Account;

use Keystone\Http\Controllers\BaseController;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Keystone\Core\User\UserSecuritySettingsService;
use Keystone\Domain\User\CurrentUser;

final class AccountSecurityController extends BaseController {

    public function __construct(
        private UserSecuritySettingsService $settings,
        private CurrentUser $currentUser,
        private Twig $view
    ) {}

    public function show(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        
        $user = $this->currentUser->user();

        return $this->view->render($response, '@core/account/security.twig', [
            'settings' => $this->settings->get($user->id()),
        ]);
    }

    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $user = $this->currentUser->user();
        $data = $request->getParsedBody();

        $this->settings->update(
            $user->id(),
            isset($data['notify_new_ip']),
            isset($data['notify_failed_logins']),
        );

        return $response
            ->withHeader('Location', '/account/security')
            ->withStatus(302);
    }
}


?>