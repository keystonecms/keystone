<?php

namespace Keystone\Security\IpInfo;

use GuzzleHttp\Client;

final class IpInfoClient {

public function __construct(
        private Client $http,
        private string $token
    ) { }

    public function fetch(string $ip): array
    {
        $response = $this->http->get(
            "https://ipinfo.io/{$ip}",
            ['query' => ['token' => $this->token]]
        );

        return json_decode(
            $response->getBody()->getContents(),
            true
        );
    }
}


?>