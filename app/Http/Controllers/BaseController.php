<?php

namespace Keystone\Http\Controllers;

use Psr\Http\Message\ResponseInterface;

abstract class BaseController {

    protected function json(
        ResponseInterface $response,
        array $data,
        int $status = 200
    ): ResponseInterface {
        $payload = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}


?>