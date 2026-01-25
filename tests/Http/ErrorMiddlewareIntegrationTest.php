<?php

declare(strict_types=1);

namespace Keystone\Tests\Http;

use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Keystone\Tests\TestCase;


final class ErrorMiddlewareIntegrationTest extends TestCase {
 
public function test_404_is_handled(): void {
        $app = $this->createApp();

        $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
            ->createServerRequest('GET', '/does-not-exist')
            ->withHeader('Accept', 'application/json');

        $response = $app->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

public function test_500_returns_error_reference(): void {
    $app = $this->createApp();

    // Route die expres crasht
    $app->get('/boom', function () {
        throw new \RuntimeException('Boom');
    });

    $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
        ->createServerRequest('GET', '/boom')
        ->withHeader('Accept', 'application/json'); // 👉 forceer JSON-pad

    $response = $app->handle($request);

    // 1️⃣ Statuscode
    $this->assertSame(500, $response->getStatusCode());

    // 2️⃣ JSON response
    $this->assertStringContainsString(
        'application/json',
        $response->getHeaderLine('Content-Type')
    );

    // 3️⃣ ERR-string in body
    $body = (string) $response->getBody();

    $this->assertMatchesRegularExpression(
        '/ERR-[A-Z0-9]+/',
        $body
    );
}

}


?>
