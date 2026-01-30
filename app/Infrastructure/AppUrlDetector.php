<?php

namespace Keystone\Infrastructure;

use Psr\Http\Message\ServerRequestInterface;

final class AppUrlDetector {

    public static function detect(ServerRequestInterface $request): string {

        $uri = $request->getUri();
        $scheme = $uri->getScheme() ?: 'http';
        $host   = $uri->getHost();
        $port   = $uri->getPort();

        $url = $scheme . '://' . $host;

        if ($port && !in_array([$scheme, $port], [['http',80], ['https',443]], true)) {
            $url .= ':' . $port;
        }

        return $url;
    }
}

?>