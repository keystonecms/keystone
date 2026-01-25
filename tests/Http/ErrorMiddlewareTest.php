<?php

declare(strict_types=1);

namespace Keystone\Tests\Http;

use Keystone\Tests\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Keystone\Http\Middleware\AuthMiddleware;
use Slim\Exception\HttpForbiddenException;

final class ErrorMiddlewareTest extends TestCase {


public function test_404_is_handled_by_error_middleware(): void {
    $app = $this->createApp();

    $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
        ->createServerRequest('GET', '/does-not-exist');

    $response = $app->handle($request);

    $this->assertSame(404, $response->getStatusCode());
}

public function test_403_is_handled_by_error_middleware(): void {
    $app = $this->createApp();

    $app->get('/forbidden', function ($request) {
        throw new HttpForbiddenException($request);
    });

    $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
        ->createServerRequest('GET', '/forbidden');

    $response = $app->handle($request);

    $this->assertSame(403, $response->getStatusCode());
}

public function test_500_is_handled_by_error_middleware(): void {
    $app = $this->createApp();

    $app->get('/boom', function () {
        throw new \RuntimeException('Kaboom');
    });

    $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
        ->createServerRequest('GET', '/boom')
        ->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->withHeader('Accept', 'application/json');
       
    $response = $app->handle($request);

    $data = json_decode((string) $response->getBody(), true);
  
    // Sanity checks (heel belangrijk)
    $this->assertNotNull(
        $data,
        'Response is not JSON: ' . (string) $response->getBody()
    );
    $this->assertIsArray($data);

    // Jouw daadwerkelijke assertions
    $this->assertArrayHasKey('message', $data);
    $this->assertMatchesRegularExpression(
        '/^ERR-[A-F0-9]{16}$/',
        $data['message']
    );


}


}


?>
