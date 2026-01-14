<?php


namespace Tests\Http;

use Tests\Tester;

use Slim\Psr7\Factory\ServerRequestFactory;

final class ErrorHandlerTest extends Tester
{
    public function test_unknown_route_returns_404(): void
    {
        $app = $this->createApp();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/does-not-exist');

        $response = $app->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function test_forbidden_returns_403(): void
    {
        $app = $this->createApp();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/admin/pages');

        $response = $app->handle($request);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(
            ['/login'],
            $response->getHeader('Location')
        );
    }
}

?>