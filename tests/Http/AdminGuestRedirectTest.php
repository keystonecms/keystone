<?php

declare(strict_types=1);

namespace Keystone\Tests\Http;

use Keystone\Tests\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Keystone\Http\Middleware\AuthMiddleware;

final class AdminGuestRedirectTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_accessing_admin_route(): void
    {
        $app = $this->createApp();

        // 1. Registreer een bestaande admin route
        $app->get('/admin/pages', function () {
            $response = new Response();
            $response->getBody()->write('admin');
            return $response;
        });

        // 2. Voeg auth middleware toe
        $app->add(AuthMiddleware::class);

        // 3. Maak Slim PSR-7 request
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/admin/pages');

        // 4. Handle request
        $response = $app->handle($request);

        // 5. Assert redirect
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(['/login'], $response->getHeader('Location'));
    }
}


?>
