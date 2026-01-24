<?php

namespace Keystone\Security\IpInfo;

use Redis;

final class RedisIpInfoCache implements IpInfoCacheInterface {
    public function __construct(
        private Redis $redis
    ) {}

    public function get(string $ip): ?array
    {
        $key = 'ipinfo:' . $ip;

        $value = $this->redis->get($key);

        return $value ? json_decode($value, true) : null;
    }

    public function set(string $ip, array $data, int $ttl): void
    {
        $this->redis->setex(
            'ipinfo:' . $ip,
            $ttl,
            json_encode($data)
        );
    }
}

?>
