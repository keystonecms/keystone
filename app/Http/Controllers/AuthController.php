<?php

declare(strict_types=1);

/*
 * Keystone CMS
 *
 * @package   Keystone CMS
 * @author    HostingBE
 * @license   MIT
 * @link      https://keystone-cms.com
 */

namespace Keystone\Http\Controllers;

use Keystone\Domain\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController {
    public function __construct(
        private UserService $users
    ) {}

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        try {
            $user = $this->users->authenticate(
                $data['email'],
                $data['password']
            );

            $_SESSION['user_id'] = $user->id();

            return redirect('/admin');
        } catch (\RuntimeException $e) {
            return redirect('/login?error=1');
        }
    }

    public function logout(): ResponseInterface
    {
        session_destroy();
        return redirect('/login');
    }
}

?>
